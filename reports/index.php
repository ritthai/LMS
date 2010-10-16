<?php
@session_start();
@db_connect();

controller_prefix();

$PAGE_REL_URL		= "$HTMLROOT/reports";
$CONTROLLER			= 'reports';
$CONTROLLER_PERMS	= 'admin';
$ACTIONS			= array(
	'create'	=> new HttpAction("$PAGE_REL_URL/create", 'post',
					array('uid', 'cid', 'comments'),
					'any'),
	'list'		=> new HttpAction("$PAGE_REL_URL/list", 'get',
					array(),
					'admin'),
	'show'		=> new HttpAction("$PAGE_REL_URL/show", 'get',
					array('id'),
					'admin'),
	);

$PAGE_TITLE = "Reported Resources";
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
if($action && $ACTIONS[$action]) {
	check_perms($ACTIONS[$action]);
} else { // index
	check_perms(User::HasPermissions($CONTROLLER_PERMS) || $bypass_auth);
}


if($action == 'show') {
	$id = $params['id'];
	$attribs = ResourceReport::Get($id);
	$args['info'] = array();
	$attribs['user_name'] = User::GetAttrib($attribs['user_id'], 'name');
	$attribs['comment_subject'] = Comment::GetSubject($attribs['comment_id']);
	if(!$attribs) {
		Error::generate('notice', 'Invalid ID in action show.');
		header("Location: $PAGE_REL_URL");
	} else {
		foreach($attribs as $k=>$v) {
			$args['info'][] = array($k, $v);
		}
		include("views/show.view.php");
	}
} else if($action == 'create') {
	if(!$params['uid'] || !User::IsAuthenticated()) {
        check_perms(false);
	} else if(!ResourceReport::Create(array('user_id'=>User::GetAuthenticatedID(), 'comment_id'=>$params['cid'], 'comments'=>$params['comments']))) {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-Type: text/html");
		header("Connection:");
		header("Content-length:");
		echo "Could not report.";
	} else {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-Type: text/html");
		header("Connection:");
		header("Content-length:");
		echo "Successfully reported.";
	}
} else if(isset($_GET['action']) && $_GET['action'] != "") { // Action with no params
	$action = $_GET['action'];
	switch($action) {
	case 'list':
		$args['list'] = ResourceReport::ListAll();
		foreach($args['list'] as $k=>$v) {
			$args['list'][$k]['user_name'] = User::GetAttrib($v['user_id'], 'name');
			$args['list'][$k]['comment_subject'] = Comment::GetSubject($v['comment_id']);
		}
		include("views/$action.view.php");
		break;
	case 'show':
		Error::generate('notice', 'Invalid file ID', Error::$FLAGS['single']);
		header("Location: $PAGE_REL_URL");
		break;
	default:
		Error::generate('suspicious', "Invalid action $action in resource reports controller");
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
