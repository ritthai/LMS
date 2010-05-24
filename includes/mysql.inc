<?php
$dbhost = 'localhost';
$dbuser = 'LMS_development';
$dbpass = '';
$dbname = 'LMS_development';

$h = null;

function database_connect() {
	global $dbuser, $dbpass, $dbname, $dbhost, $h;
	$h = mysql_connect($dbhost, $dbuser, $dbpass) or die("mysql_connect error: ".mysql_error());
	mysql_select_db($dbname, $h) or die("mysql_select_db error: ".mysql_error());
}

function database_query($query) {
	global $dbuser, $dbpass, $dbname, $dbhost, $h;
	$status = mysql_query($query);
	Error::generate('debug', 'mysql query: '.$query);
	if(!$status)
		Error::generate('warn', "Database error: ".mysql_error());
	return $status;
}

function database_close() {
	global $dbuser, $dbpass, $dbname, $dbhost, $h;
	mysql_close($h);
}
?>
