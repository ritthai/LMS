<?php
Error::generate('debug', "Loading $CONTROLLER/$action");
if(!session_id()) @session_start();
if($CONFIG['debug']) $args['pagetitle'] .= ' - Debugging Mode';
// JKOFF: Watch this code, it could cause problems in the future.
if(isset($_SESSION)
		&& $_SERVER['REQUEST_URI'] != '/user/login'
		&& $_SESSION[$action] != 'login' )
{
	Error::generate('debug', 'Rendering normal. Last rendered page: '
								. $_SESSION['last_rendered_page']);
	$_SESSION['last_rendered_page'] = $_SERVER['REQUEST_URI'];
	$_SESSION['last_rendered_controller'] = $CONTROLLER;
	$_SESSION['last_rendered_action'] = $action;
} else {
	Error::generate('debug', 'Rendering login. Last rendered page: '
								. $_SESSION['last_rendered_page']);
}

/**
	Given:	pagetitle
*/
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
		<title><?php echo $args['pagetitle']; ?></title>
