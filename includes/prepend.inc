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
ini_set('user_agent', $CONFIG['user_agent']);

profiling_start('all');

if($CONFIG['debug'] === true) {
	declare(ticks=20);
}
function memstr($bytes) {
	if($bytes > 1048576) return number_format($bytes/1048576, 2).'M';
	else if($bytes > 1024) return number_format($bytes/1024, 0).'K';
	else return $bytes.'B';
}
function tick_handler() {
	$memusage = memstr(memory_get_usage());
	$memrealusage = memstr(memory_get_usage(true));
	$mempeakusage = memstr(memory_get_peak_usage());
	$mempeakrealusage = memstr(memory_get_peak_usage(true));
	Error::setMemStats("Memory used: $memusage/$mempeakusage<br>Real: $memrealusage/$mempeakrealusage");
}
register_tick_function('tick_handler');

$GLOBALS['userinfo_cache'] = array();

