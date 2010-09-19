<?php

$mysqli = false;
$mysqli_res = false;

function db_connect() {
	global $CONFIG, $mysqli;

	$success = true;
	profiling_start('mysqli_connect');
	$mysqli = mysqli_init();
	if(!$mysqli) {
		Error::generate('debug', 'Failed mysqli_connect');
		$success = false;
		goto end;
	}
	$status = $mysqli->real_connect($CONFIG['dbhost'], $CONFIG['dbuser'], $CONFIG['dbpass'], $CONFIG['dbname']);
	if(!$status) {
		Error::generate('debug', 'Failed mysqli_real_connect ('.mysqli_connect_errno().'): '.mysqli_connect_error());
		$success = false;
		goto end;
	}
	end:
	if(!$success) {
		Error::generate('fatal', 'Failed to connect to database. Please try again later.');
	}
	profiling_end('mysqli_connect');
}

function db_query(/* fmt, ... */) {
	global $mysqli, $mysqli_res;

	profiling_start('mysqli');

	$argv = func_get_args();
	$fmt = array_shift($argv);
	foreach($argv as $k=>$v) {
		if(is_string($v)) {
			$argv[$k] = db_real_escape_string($v);
		}
	}
	$query = vsprintf($fmt, $argv);
	$mysqli_res = $mysqli->query($query);
	$clean_query = preg_replace('/\s+/', ' ', $query);
	Error::setPrepend($clean_query);
	profiling_end('mysqli', $query);
	if(!$mysqli_res) {
		Error::generate('debug', "Database error: ".db_get_error());
	}
	
	return $mysqli_res;
}

function db_close() {
	global $mysqli;
	$mysqli->close();
}


function db_get_list_of_results($res) {
	return array_map(	function ($elem) { return $elem[0]; },
						_db_get_rows(MYSQLI_NUM) );
}
function db_get_list_assoc($res) {
	return _db_get_rows(MYSQLI_ASSOC);
}
function db_get_list_of_assoc($res) {
	return _db_get_rows(MYSQLI_ASSOC);
}
function db_get_list_of_list_results($res) {
	return _db_get_rows(MYSQLI_NUM);
}
function db_get_list_result($res) {
	return _db_get_row(MYSQLI_NUM);
}
function db_get_assoc($res) {
	return _db_get_row(MYSQLI_ASSOC);
}
function db_get_result($res) {
	$tmp = _db_get_row(MYSQLI_NUM);
	return $tmp[0];
}
function _db_get_row($type) {
	global $mysqli, $mysqli_res;
	$ret = $mysqli_res->fetch_array($type);
	$mysqli_res->close();
	if(!$ret) {
		Error::generate('debug', 'Row not found in _db_get_row.');
		return false;
	} else {
		Error::setPrepend($ret);
		Error::generate('debug', 'Query result _db_get_row');
		return $ret;
	}
}
function _db_get_rows($type) {
	global $mysqli, $mysqli_res;
	$ret = $mysqli_res->fetch_all($type);
	$mysqli_res->close();
	if(!$ret) {
		Error::generate('debug', 'Row not found in _db_get_rows.');
		return array();
	} else {
		Error::setPrepend($ret);
		Error::generate('debug', 'Query result _db_get_rows');
		return $ret;
	}
}

function db_insert_id() {
	global $mysqli;
	return $mysqli->insert_id;
}
function db_affected_rows() {
	global $mysqli;
	return $mysqli->affected_rows;
}
function db_real_escape_string($val) {
	global $mysqli;
	return $mysqli->real_escape_string($val);
	return '';
}

function db_get_errno() {
	global $mysqli;
	if($mysqli) return $mysqli->errno();
	else return 0;
}
function db_get_error() {
	global $mysqli;
	if($mysqli) return $mysqli->error;
}

function db_start_transaction($table, $id, $blocking=true) {
	global $mysqli;
	profiling_start('db_start_transaction');
	$silent = false;
	$affected = 0;
	$timeout = 60000000; // 1 min
	db_query("UPDATE %s_lock SET locked='1' WHERE id='%s'", $table, $id);
	$affected = db_affected_rows();
	while($timeout > 0 && $blocking && $affected == 0) { // locked, so spinlock
		if(!$silent) Error::generate('debug', "Row already locked: table=$table, id=$id");
		usleep(500000);
		$timeout -= 500000;
		db_query("UPDATE %s_lock SET locked='1' WHERE id='%s'", $table, $id);
		$affected = db_affected_rows();
		$silent = true;
	}
	profiling_end('db_start_transaction');
	return !$silent && ($affected > 0); // success on first try?
}
function db_check_transaction($table, $id) {
	global $mysqli;
	db_query("SELECT locked FROM %s_lock WHERE id='%s'", $table, $id);
	return db_get_result(0);
}
function db_end_transaction($table, $id) {
	global $mysqli;
	db_query("UPDATE %s_lock SET locked='0' WHERE id='%s'", $table, $id);
	if(db_affected_rows() == 0)	{
		Error::generate('debug', "Could not unlock row: table=$table, id=$id");
		return false;
	}
	return true;
}
