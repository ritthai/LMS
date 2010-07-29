<?php

$h = null;

function db_connect() {
	global $CONFIG, $h;

	Error::generate('debug', 'db_connect()');
	$h = @mysql_connect($CONFIG['dbhost'], $CONFIG['dbuser'], $CONFIG['dbpass']) or die("mysql_connect error: ".mysql_error());
	@mysql_select_db($CONFIG['dbname'], $h) or die("mysql_select_db error: ".mysql_error());
}

function db_query(/* fmt, ... */) {
	global $h;

	profiling_start('mysql');

	$argv = func_get_args();
	$fmt = array_shift($argv);
	foreach($argv as $k=>$v) {
		if(is_string($v)) {
			$argv[$k] = mysql_real_escape_string($v);
		}
	}
	$query = vsprintf($fmt, $argv);
	$res = @mysql_query($query);
	Error::generate('debug', 'mysql query: '.$query);
	if(!$res)
		Error::generate('debug', "Database error: ".mysql_error());

	profiling_end('mysql');

	return $res;
}

function db_close() {
	global $h;
	@mysql_close($h);
}

function db_get_list_result($res) {
	$ret = array();
	while($row = @mysql_fetch_row($res)) {
		array_push( $ret, $row );
	}
	@mysql_free_result($res);
	return $ret;
}

function db_get_list_of_results($res) {
	$ret = array();
	while($row = @mysql_fetch_row($res)) {
		Error::generate('debug', 'mysql result: '.$row[0]);
		array_push( $ret, $row[0] );
	}
	@mysql_free_result($res);
	return $ret;
}

function db_get_assoc($res) {
	$ret = @mysql_fetch_assoc($res);
	@mysql_free_result($res);
	if(!$ret) {
		Error::generate('debug', 'Row not found in database_get_result.');
		return false;
	} else {
		return $ret;
	}
}

function db_get_result($res) {
	$ret = @mysql_fetch_row($res);
	@mysql_free_result($res);
	if(!$ret) {
		Error::generate('debug', 'Row not found in database_get_result.');
		return false;
	} else {
		Error::generate('debug', 'mysql result: '.$ret[0]);
		return $ret[0];
	}
}

function db_get_errno() {
	return mysql_errno();
}

?>
