<?php
@include("$ROOT/includes/mysql.inc");

@session_start();
@db_connect();
@User::init();

$PAGE_REL_URL = "$HTMLROOT/user";

$ACTIONS = array(
	'create' => new HttpAction("$PAGE_REL_URL/create", 'post',
				array('name', 'email', 'password', 'role')),
	'list' => new HttpAction("$PAGE_REL_URL/list", 'get',
				array()),
	'show' => new HttpAction("$PAGE_REL_URL/show", 'get',
				array('userid')),
	'login' => new HttpAction("$PAGE_REL_URL/login", 'post',
				array('name', 'password')),
	'logout' => new HttpAction("$PAGE_REL_URL/logout", 'get',
				array()),
	'status' => new HttpAction("$PAGE_REL_URL/status", 'get',
				array())
	);

$PAGE_TITLE = "User management";
if($CONFIG['debug']) $PAGE_TITLE .= " - Debugging Mode";
$args = array(	'pagetitle'	=> $PAGE_TITLE,
				'actions'	=> $ACTIONS	);

Error::generate('debug', 'Loading page: '.$PAGE_TITLE);

if($ACTIONS['create']->wasCalled()) {
	$params = $ACTIONS['create']->getParams();
	$filtered_params = array();
	$authorized = true;
	foreach($params as $k=>$v) {
		switch($k) {
		case 'role':
			if(!User::HasPermissions('admin')) {
				Error::generate('suspicious', 'Insufficient permissions to create user with role');
				Error::generate('notice', 'Insufficient permissions to create user with role');
				$authorized = false;
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
	if($authorized) $userid = User::Create($filtered_params);
	else $userid = 0;
	if($userid > 0) {
		Error::generate('notice', 'Account created!');
	} else {
		Error::generate('notice', 'Account creation failed');
		header("Location: $PAGE_REL_URL/create");
	}
	header("Location: $PAGE_REL_URL");
} else if($ACTIONS['show']->wasCalled()) {
    $params = $ACTIONS['show']->getParams();
	$args['userinfo'] = User::GetAttribs($params['userid']);
    eval("?>".file_get_contents("views/show.view.php"));
} else if($ACTIONS['login']->wasCalled()) {
	$params = $ACTIONS['login']->getParams();
	$res = User::Authenticate($params['name'], $params['password']);
	if($res) {
		Error::generate('notice', 'Authentication successful');
		header("Location: $PAGE_REL_URL");
	} else {
		Error::generate('notice', 'Invalid username/password combination');
		eval("?>".file_get_contents("views/login.view.php"));
	}
} else if($ACTIONS['logout']->wasCalled()) {
	$res = User::Deauthenticate();
	if($res)
		Error::generate('notice', 'Logged out successfully');
	else
		Error::generate('notice', 'Not logged in');
	header("Location: $PAGE_REL_URL");
} else if($ACTIONS['status']->wasCalled()) {
	$authid = User::GetAuthenticatedID();
	if(!$authid) {
		Error::generate('notice', 'Not logged in');
		// watch out, execution continues after a header() call
		header("Location: $PAGE_REL_URL");
	} else {
		$args['userinfo'] = User::GetAttribs($authid);
		eval("?>".file_get_contents("views/show.view.php"));
	}
} else if(isset($_GET['action']) && $_GET['action'] != "") { // Action with no params
	$action = $_GET['action'];
	switch($action) {
	case 'list':
		$args['userlist'] = User::ListAll();
		// Fallthrough
	case 'create':
	case 'login':
		eval("?>".file_get_contents("views/$action.view.php"));
		break;
	case 'show':
		Error::generate('notice', 'Invalid user ID');
		header("Location: $PAGE_REL_URL");
		break;
	default:
		header("Location: $PAGE_REL_URL");
	}
} else {
	eval("?>".file_get_contents("views/index.view.php"));
}

db_close();
?>
