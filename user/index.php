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
				array('name', 'password'))
	);

$PAGE_TITLE = "User management";
if($CONFIG['debug']) $PAGE_TITLE .= " - Debugging Mode";
$args = array(	'pagetitle'	=> $PAGE_TITLE,
				'actions'	=> $ACTIONS	);

Error::generate('debug', 'Loading page: '.$PAGE_TITLE);

if($ACTIONS['create']->wasCalled()) {
	$params = $ACTIONS['create']->getParams();
	$filtered_params = array();
	foreach($params as $k=>$v) {
		switch($k) {
		case 'role':
			// authenticate as admin
		case 'name':
		case 'email':
		case 'password':
			$filtered_params[$k] = $v;
			break;
		default:
			// complain about extra params?
		}
	}
	$userid = User::Create($filtered_params);
	if($userid > 0)
		Error::generate('fatal', 'Account created!');
	else
		Error::generate('fatal', 'Account creation failed.');
	header("Location: $PAGE_REL_URL");
} else if($ACTIONS['show']->wasCalled()) {
    $params = $ACTIONS['show']->getParams();
	$args['userinfo'] = User::GetAttribs($params['userid']);
    //$args['userinfo']['id'] = $params['userid'];
	//foreach(User::GetAttribs($args['userinfo']['id']) as $attrib
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
} else if(isset($_GET['action']) && $_GET['action'] != "") { // Action with no params
	$action = $_GET['action'];
	switch($action) {
	case 'list':
		$args['userlist'] = User::ListAll();
		// Fallthrough
	case 'show':
	case 'create':
	case 'login':
		eval("?>".file_get_contents("views/$action.view.php"));
		break;
	default:
		header("Location: $PAGE_REL_URL");
	}
} else {
	eval("?>".file_get_contents("views/index.view.php"));
}

db_close();
?>
