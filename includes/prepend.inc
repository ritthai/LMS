<?php
$ROOT = '/Library/WebServer/Documents'; // Also change prepend file in .htaccess
$HTMLROOT = '';//'http://localhost';
$TEMPLATEROOT = "$ROOT/includes/template";

function __autoload($cname) {
	global $ROOT;
	require_once($ROOT . '/classes/' . strtolower($cname) . '.class.php');
}

@include_once("$ROOT/includes/profiling.inc");
@include_once("$ROOT/includes/prefix.inc");
@include_once("$ROOT/includes/config.inc");
@include_once("$ROOT/includes/util.inc");
@include_once("$ROOT/includes/misc.inc");
@include_once("$ROOT/includes/mysql.inc");

header('X-Powered-By: Pigeons');
?>
