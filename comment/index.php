<?php
@include("$ROOT/includes/mysql.inc");
@include("$ROOT/includes/prefix.inc");

@session_start();
@db_connect();

controller_prefix();

$PAGE_REL_URL		= "$HTMLROOT/comment";
$CONTROLLER			= 'comment';
$CONTROLLER_PERMS	= 'admin';
$ACTIONS			= array(
	'create' =>		new HttpAction("$PAGE_REL_URL/create", 'post',
						array('subject', 'body')),
	'create2' =>	new HttpAction("$PAGE_REL_URL/show", 'post',
						array('subject', 'body', 'id')),
	//'get' =>		new HttpAction("$PAGE_REL_URL/get", 'get',
	//					array('id'),
	//					'any'),
	'list' =>		new HttpAction("$PAGE_REL_URL/list", 'get',
						array()),
	'show' =>		new HttpAction("$PAGE_REL_URL/show", 'get',
						array('id')),
	);

$PAGE_TITLE = "Comment management";
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

if($action == 'create2') {
	$action = 'create';
} else if($action == 'create') {
	$params['id'] = 1;
}

if($action && $ACTIONS[$action]) {
	check_perms($ACTIONS[$action]);
} else { // index
	check_perms(User::HasPermissions($CONTROLLER_PERMS));
}

if($action == 'create') {
	$params['owner'] = User::GetAuthenticatedID();
	if(!$params['owner']) {
		check_perms(false);
	} else if(!Comment::Create($params)) {
		Error::generate('warn', 'Could not create comment.', Error::$FLAGS['single']);
		include('views/create.view.php');
	} else {
		Error::generate('success', 'Comment created.', Error::$FLAGS['single']);
		$args['list'] = Comment::ListAll();
		redirect('comment', 'list');
	}
} else if($action == 'list') {
	$args['list'] = Comment::ListAll();
	include('views/list.view.php');
} else if($action == 'show') {
	$args['info'] =
		array(	array('id',			$params['id']),
				array('subject',	Comment::GetSubject($params['id'])),
				array('created on',	Comment::GetTimestamp($params['id'])),
				array('body',		Comment::GetAttrib($params['id'], 'body'))
				);
	include('views/show.view.php');
} else if(isset($_GET['action']) && $_GET['action'] != "") { // Action with no params
	$action = $_GET['action'];
	switch($action) {
	case 'create':
		include('views/create.view.php');
		break;
	default:
		Error::generate('suspicious', "Invalid action $action in /file/");
		redirect();
	}
} else {
	include('views/index.view.php');
}

db_close();
?>
