<?php
@session_start();
@db_connect();

controller_prefix();

$PAGE_REL_URL		= "$HTMLROOT/file";
$UPLOAD_ROOT		= "file/uploads";
$CONTROLLER			= 'file';
$CONTROLLER_PERMS	= 'admin';
$ACTIONS			= array(
	// Files are created through the file controller
	//'create' => new HttpAction("$PAGE_REL_URL/create", 'post',
	//			array('name', 'email', 'password')),
	'list' => new HttpAction("$PAGE_REL_URL/list", 'get',
				array(),
				'admin'),
	'show' => new HttpAction("$PAGE_REL_URL/show", 'get',
				array('id'),
				'any'),
	'get' => new HttpAction("$PAGE_REL_URL/get", 'get',
				array('id'),
				'any'),
	);

$PAGE_TITLE = "File management";
$args = array(	'pagetitle'	=> $PAGE_TITLE,
				'actions'	=> $ACTIONS	);

$action = false;
$params = array();
foreach($ACTIONS as $key => $val) {
    if($val->wasCalled()) {
        if(!$action) $action = $key;
        $params = array_merge($params, $val->getParams());
    }
}

$bypass_auth = false;
if($params['id'] && $action == 'show') { // owner can always see his file
	$owner = File::GetAttrib($params['id'], 'owner');
	$bypass_auth = $owner && $owner == User::GetAuthenticatedID();
}
if($action && $ACTIONS[$action]) {
	check_perms($ACTIONS[$action]);
} else { // index
	check_perms(User::HasPermissions($CONTROLLER_PERMS) || $bypass_auth);
}


if($action == 'show') {
	$id = $params['id'];
	$args['fileinfo'] = File::GetAttribs($id);
	if(!$args['fileinfo']) {
		Error::generate('notice', 'Invalid file ID in action show.');
		header("Location: $PAGE_REL_URL");
	} else {
		foreach($args['fileinfo'] as $key=>$param) {
			switch(strtolower($param[0])) {
			case 'path':
				$path = $param[1];
				$link = $ACTIONS['get']->getLink(array('id'=>$id));
				$args['fileinfo'][$key][1] = "<a href=\"$link\">$path</a>";
				break;
			default:
			}
		}
		include("views/show.view.php");
	}
} else if($action == 'get') {
	$id = $params['id'];
	$name = File::GetAttrib($id, 'name');
	$path = File::GetAttrib($id, 'path');
	$ext = File::GetAttrib($id, 'type');
	Error::generate('debug', "id=$id, name=$name, path=$path, ext=$ext");
	if(!$path) {
		//Error::generate('notice', 'File not found.', Error::$FLAGS['single']);
		//header('Location: '.getLastVisited());
	} else {
		$content_type = "";
		$content_disposition = "inline";
		switch(strtolower($ext)) {
			case 'jpg':
				$content_type = 'image/jpeg';
				break;
			case 'png':
			case 'gif':
			case 'jpeg':
				$content_type = "image/$ext";
				break;
			case 'htm':
				$content_type = 'text/html';
				break;
			case 'plain':
			case 'html':
				$content_type = "text/$ext";
				break;
			case 'mpg':
				$content_type = 'video/mpeg';
				break;
			case 'mov':
				$content_type = 'video/quicktime';
				break;
			case 'wmv':
				$content_type = 'video/x-ms-wmv';
				break;
			case 'mpeg':
			case 'mp4':
			case 'ogg':
				$content_type = "video/$ext";
				break;
			case 'pdf':
				$content_type = 'application/pdf';
				break;
			default:
				$content_type = 'application/force-download';
				$content_disposition = 'attachment';
				header('Expires: 0');
				header('Cache-Control: private');
				header('Pragma: cache');
				header('Content-Type: application/octet-stream');
				header('Content-Type: application/download');
				header('Content-Transfer-Encoding: binary');
		}
		header("Content-Type: $content_type");
		header("Content-Disposition: $content_disposition; filename=$name.$ext");
		header('Content-Length: '.filesize("$ROOT/$path"));
		@readfile("$ROOT/$path");
	}
} else if(isset($_GET['action']) && $_GET['action'] != "") { // Action with no params
	$action = $_GET['action'];
	switch($action) {
	case 'list':
		$args['filelist'] = File::ListAll();
		include("views/$action.view.php");
		break;
	case 'show':
		Error::generate('notice', 'Invalid file ID', Error::$FLAGS['single']);
		header("Location: $PAGE_REL_URL");
		break;
	default:
		Error::generate('suspicious', "Invalid action $action in /file/");
		header("Location: $PAGE_REL_URL");
	}
} else {
	include("views/index.view.php");
}

db_close();

profiling_end('all');

profiling_print_summary();

Error::showSeparator();
Error::setBgColour('#B66');
Error::generate('debug', "Finished rendering $_SERVER[REQUEST_URI] normally");
Error::setBgColour('#555');
Error::showSeparator();
