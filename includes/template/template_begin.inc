<?php
Error::generate('debug', "Loading $CONTROLLER/$action");
if($CONFIG['debug']) $args['pagetitle'] .= ' - Debugging Mode';
/**
	Given:	pagetitle
*/
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
		<title><?php echo $args['pagetitle']; ?></title>
