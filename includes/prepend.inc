<?php
$ROOT = '/Library/WebServer/Documents/lms'; // Also change prepend file in .htaccess
$HTMLROOT = 'http://localhost/lms';
$TEMPLATEROOT = "$ROOT/includes/template";
include("$ROOT/includes/config.inc");
include("$ROOT/includes/util.inc");
include("$ROOT/includes/misc.inc");

header('X-Powered-By: Pigeons');

function __autoload($cname) {
	global $ROOT;
	require_once $ROOT . '/classes/' . $cname . '.class.php';
}
?>
