<?php
@require_once("$ROOT/includes/recaptchalib.php");
@require_once("$ROOT/includes/mysql.inc");
@require_once("$ROOT/includes/prefix.inc");

@session_start();
@db_connect();
@User::init();
@File::init();

controller_prefix();

$CONTROLLER = 'user';
$PAGE_REL_URL = "$HTMLROOT/$CONTROLLER";
$UPLOAD_ROOT = "$CONTROLLER/uploads";
$ACTIONS = array(
	'create' => new HttpAction("$PAGE_REL_URL/create", 'post',
				array(	'name', 'email', 'password',
						'recaptcha_challenge_field', 'recaptcha_response_field')),
	'list' => new HttpAction("$PAGE_REL_URL/list", 'get',
				array()),
	'show' => new HttpAction("$PAGE_REL_URL/show", 'get',
				array('id')),
	'login' => new HttpAction("$PAGE_REL_URL/login", 'post',
				array('name', 'password')),
	'logout' => new HttpAction("$PAGE_REL_URL/logout", 'get',
				array()),
	'status' => new HttpAction("$PAGE_REL_URL/status", 'get',
				array()),
	'forgot_password' => new HttpAction("$PAGE_REL_URL/forgot_password", 'get',
				array('name', 'email')),
	'reset_password' => new HttpAction("$PAGE_REL_URL/reset_password", 'get',
				array('key')),
	'finish_reset_password' => new HttpAction("$PAGE_REL_URL/reset_password", 'post',
				array('key', 'id', 'password')),
	'upload' => new HttpAction("$PAGE_REL_URL/upload", 'post',
				array('MAX_FILE_SIZE', 'name', 'comment'))
	);

$PAGE_TITLE = "User management";
$args = array(	'pagetitle'	=> $PAGE_TITLE,
				'actions'	=> $ACTIONS	);

$allowed_upload_extensions = array(	"txt", "csv", "htm", "html", "xml",
									"css", "doc", "xls", "rtf", "ppt", "pdf", "swf", "flv", "avi",
									"wmv", "mov", "jpg", "jpeg", "gif", "png");

$action = false;
$params = array();
foreach($ACTIONS as $key => $val) {
    if($val->wasCalled()) {
        if(!$action) $action = $key;
        $params = array_merge($params, $ACTIONS[$action]->getParams());
		break;
    }
}

if($action == 'create') {
	$filtered_params = array();
	$authorized = true;
	$recaptcha_resp = recaptcha_check_answer(	$CONFIG['recaptcha_prikey'], $_SERVER['REMOTE_ADDR'],
												$params['recaptcha_challenge_field'], $params['recaptcha_response_field'] );
	if(!$recaptcha_resp->is_valid) {
		$args['recaptcha_error'] = $recaptcha_resp->error;
		Error::generate('notice', 'Incorrect captcha answer.');
		$authorized = false;
	}
	foreach($params as $k=>$v) {
		switch($k) {
		case 'role':
			if(!User::HasPermissions('admin')) {
				Error::generate('suspicious', 'Insufficient permissions to create user with role');
				Error::generate('notice', 'Insufficient permissions to create user with role');
				$authorized = false;
				break;
			}
		case 'name':
		case 'email':
		case 'password':
			$filtered_params[$k] = $v;
			break;
		default:
			// complain about extra params?
		}
	}
	if($authorized) $id = User::Create($filtered_params);
	else $id = 0;
	if($id > 0) {
		Error::generate('notice', 'Account created!');
		redirect();
	} else {
		Error::generate('notice', 'Account creation failed', Error::$FLAGS['single']);
		redirect('create');
	}
} else if($action == 'show' || ($status_called = ($action == 'status'))) {
	$id = $status_called ? User::GetAuthenticatedID() : $params['id'];
	if(!$id) {
		Error::generate('notice', 'Must be logged in.');
		redirect();
	} else if(!($args['userinfo'] = User::GetAttribs($id))) {
		Error::generate('notice', 'Invalid user ID.');
		redirect();
	} else {
		foreach($args['userinfo'] as $key=>$param) {
			switch(strtolower($param[0])) {
				case 'file':
					$id = $param[1];
					$fname = File::GetAttrib($id, 'name');
					$frole = File::GetAttrib($id, 'roles');
					$fowner = File::GetAttrib($id, 'owner');
					if(!User::HasPermissions($frole)) { // if you can see it, you're the owner
						$prefix = '(Pending approval) ';
					} else {
						$prefix = '';
					}
					if(User::HasPermissions($frole) || User::GetAuthenticatedID() == $fowner) {
						$args['userinfo'][$key] = array($prefix.$param[0], "<a href=\"$HTMLROOT/file/show?id=$id\">$fname</a>");
					} else {
						unset($args['userinfo'][$key]);
					}
					break;
				default:
			}
		}
		include("views/show.view.php");
	}
} else if($action == 'login') {
	session_regenerate_id();
	$res = User::Authenticate($params['name'], $params['password']);
	if($res) {
		Error::generate('notice', 'Authentication successful');
		if(isset($_SESSION) && $_SESSION['last_rendered_page']) {
			redirect_raw($_SESSION['last_rendered_page']);
		} else {
			redirect();
		}
	} else {
		Error::generate('notice', 'Invalid username/password combination', Error::$FLAGS['single']);
		include("views/login.view.php");
	}
} else if($action == 'forgot_password') {
	$name = $params['name'];
	$email = User::GetAttrib(User::GetUserID($name), 'email');
	if($email != $params['email']) {
		Error::generate('notice', 'Invalid email address and/or username');
		redirect();
	} else {
		$key = User::GenerateForgottenPasswordKey($name);
		$hdr = "From: jkoff@129-97-224-169.uwaterloo.ca";
		$msg = "Follow the following URL to reset your password:\
				$PAGE_REL_URL/reset_password?key=$key";
		// UWaterloo blocks SMTP (port 25) outgoing
		$res = mail("$name <$email>", 'Password Reset', $msg, $hdr);
		Error::generate('debug', $msg);
		if($res)
			Error::generate('notice', 'Password reset instructions were sent to the email address associated with your account.');
		else
			Error::generate('notice', 'Could not send password reset email.');
		redirect();
	}
} else if($action == 'reset_password') {
	if(!$id = User::ValidateForgottenPasswordKey($params['key'])) {
		Error::generate('notice', 'Invalid URL');
		redirect();
	} else if(!isset($params['id'])) { // stage 1 - ask for new password
		$args['id'] = $id;
		$args['key'] = $params['key'];
		include("views/reset_password.view.php");
	} else { // stage 2 - reset password
		$ret = User::SetAttrib($id, 'password', $params['password']);
		if($ret)
			Error::generate('notice', 'Your password was set successfully. You may now log in.');
		else
			Error::generate('notice', 'Your password could not be reset.', Error::$FLAGS['single']);
		redirect();
	}
} else if($action == 'upload') {
	$ext = end(explode('.', $_FILES['file']['name']));
	// TODO: Check file extension.
	if(!isset($_FILES['file'])) {
		Error::generate('notice', 'No file specified.');
		include("views/upload.view.php");
	} else if(!User::IsAuthenticated()) {
		Error::generate('notice', 'Not logged in.');
		include("views/upload.view.php");
	} else if($_FILES['file']['error'] != UPLOAD_ERR_OK) {
		Error::generate('debug', 'File upload error: '.$_FILES['file']['error']);
		switch($_FILES['file']['error']) {
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			Error::generate('notice', 'File too big.');
			include("views/upload.view.php");
			break;
		case UPLOAD_ERR_PARTIAL:
		case UPLOAD_ERR_NO_TMP_DIR:
		case UPLOAD_ERR_CANT_WRITE:
		case UPLOAD_ERR_EXTENSION:
		default:
			Error::generate('debug', 'File upload error: '.$_FILES['file']['error']);
			Error::generate('notice', 'Could not upload file.');
			include("views/upload.view.php");
			break;
		case UPLOAD_ERR_NO_FILE:
			Error::generate('notice', 'No file specified.');
			include("views/upload.view.php");
			break;
		}
	} else if(!in_array($ext, $allowed_upload_extensions)) {
		Error::generate('notice', 'Invalid file extension.');
		include("views/upload.view.php");
	} else {
		$id = User::GetAuthenticatedID();
		$upload_dir = $UPLOAD_ROOT.'/'.$id.'/';
		$upload_path = $upload_dir.hash('sha256', $_FILES['file']['name']);
		if(!file_exists("$ROOT/$upload_dir")) {
			mkdir("$ROOT/$upload_dir");
		}
		$fileCfg = array(	'name'		=> $params['name'],
							'path'		=> $upload_path,
							'owner'		=> $id,
							'roles'		=> 'admin',
							'type'		=> $ext,
							'comment'	=> $params['comment'] );
		$res = File::Create($fileCfg, $_FILES['file']['tmp_name']);
		if($res && User::SetAttrib($id, 'file', $res)) {
			Error::generate('notice', 'File was successfully uploaded, and is pending approval by an administrator.');
		} else {
			Error::generate('notice', 'Could not upload file.', Error::$FLAGS['single']);
		}
		redirect();
	}
} else if(isset($_GET['action']) && $_GET['action'] != "") { // Action with no params
	$action = $_GET['action'];
	switch($action) {
	case 'status':
		// This should never happen.
		Error::generate('debug', 'In case \'status\': in action with no params in user controller');
		redirect();
		break;
	case 'logout':
		$res = User::Deauthenticate();
		if($res) {
			Error::generate('notice', 'Logged out successfully');
		} else {
			Error::generate('notice', 'Not logged in');
		}
		if(isset($_SESSION['last_rendered_page']) && $_SESSION['last_rendered_page']) {
			redirect_raw($_SESSION['last_rendered_page']);
		} else {
			redirect();
		}
		break;
	case 'list':
		$args['userlist'] = User::ListAll();
		// Fallthrough
	case 'create':
	case 'login':
	case 'forgot_password':
	case 'reset_password':
	case 'upload':
		include("views/$action.view.php");
		break;
	case 'show':
		Error::generate('notice', 'Invalid user ID', Error::$FLAGS['single']);
		redirect();
		break;
	default:
		Error::generate('suspicious', "Invalid action $action in /user/");
		redirect();
	}
} else {
	include("views/index.view.php");
}


db_close();
?>
