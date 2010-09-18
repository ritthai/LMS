<?php
profiling_end('action');
profiling_start('view');

Error::generate('debug', "Loading $CONTROLLER/$action");
if(!session_id()) @session_start();
if($CONFIG['debug']) $args['pagetitle'] .= ' - Debugging Mode';
// JKOFF: Watch this code, it could cause problems in the future.
if($_SERVER['REQUEST_URI'] != '/user/login' && $_SERVER['REQUEST_URI'] != '/user/create') {
	Error::generate('debug', 'Rendering normal. Last rendered page: '
								. $_SESSION['last_rendered_page']);
	$_SESSION['last_rendered_page'] = $_SERVER['REQUEST_URI'];
	$_SESSION['last_rendered_controller'] = $CONTROLLER;
	$_SESSION['last_rendered_action'] = $action;
} else {
	Error::generate('debug', 'Rendering login. Last rendered page: '.$_SESSION['last_rendered_page']);
}

Error::showSeparator();
Error::setBgColour("#000");
@include("$ROOT/includes/html_utils.inc");

/**
	Given:	pagetitle
*/
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
		<!--[if lt IE 9]><script src="/js/html5.js"></script><![endif]-->
		<link rel="stylesheet" href="/css/default.css" type="text/css" media="screen, projection" />
		<link rel="stylesheet" href="/css/autosuggest_inquisitor.css" text="text/css" media="screen,projection" />
		<link rel="stylesheet" href="/fancybox/fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
		<meta charset="utf-8" />
		<title><?php echo $args['pagetitle'].' // ClassMate'; ?></title>
		<script type="text/javascript" src="/js/global.js"> </script>
        <script type="text/javascript" src="/js/prototype-1.6.0.3.js"> </script>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"> </script>
        <!--<script type="text/javascript" src="/js/bsn.AutoSuggest_2.1.3.js"> </script>-->
        <script type="text/javascript"><!--
            jQuery.noConflict();
			var RegisteredGlobals = new Array();
<?php		jsRegisterGlobal('AuthenticatedUserAvatar', User::GetAvatar(User::GetAuthenticatedID()));	?>
<?php		jsRegisterGlobal('CourseCID', $args['comment_id']);	?>
        //--></script>
        <script type="text/javascript" src="/fancybox/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
        <script type="text/javascript" src="/js/jquery.qtip.min.js"> </script>
<?php   //if($args['university']) jsSetupAutocompleteWithId('search_form', 'search_terms', 'search_id', 'courses', 4); ?>
        <script type="text/javascript" src="/js/vidpanel.js"> </script>
		<script type="text/javascript" src="/js/comment.js"> </script>
		<script type="text/javascript" src="/js/favs.js"> </script>
