<?php
Error::generate('debug', "Loading $CONTROLLER/$action");
if(!session_id()) @session_start();
if($CONFIG['debug']) $args['pagetitle'] .= ' - Debugging Mode';
$_SESSION['last_rendered_page'] = $_SERVER['REQUEST_URI'];

/**
	Given:	pagetitle
*/
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
		<title><?php echo $args['pagetitle']; ?></title>
