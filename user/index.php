<?php
include("$ROOT/includes/mysql.inc");

session_start();
database_connect();

$PAGE_REL_URL = "$HTMLROOT/user/";

$ACTIONS = array(
	'create' => new HttpAction($PAGE_REL_URL, 'post',
				array('name', 'email', 'password', 'role')));

$PAGE_TITLE = "User management";
if($CONFIG['debug']) $PAGE_TITLE .= " - Debugging Mode";
$args = array(	'pagetitle'	=> $PAGE_TITLE,
				'actions'	=> $ACTIONS	);

if($ACTIONS['create']->wasCalled()) {
	$params = $ACTIONS['create']->getParams();
	$userid = User::create($params);
	if($userid > 0)
		Error::generate('notice', 'Account created!');
	else
		Error::generate('fatal', 'Account creation failed.');
	eval("?>".file_get_contents("views/index.view.php"));
} else if(isset($_GET['action'])) {
	$action = $_GET['action'];

	switch($action) {
	case 'create':
		eval("?>".file_get_contents("views/$action.view.php"));
		break;
	default:
		eval("?>".file_get_contents("views/index.view.php"));
	}
} else {
	eval("?>".file_get_contents("views/index.view.php"));
}

database_close();
?>
