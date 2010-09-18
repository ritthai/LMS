<?php
/** Usage:
	cache_course.php daemon_id
	Two daemons with the same daemon id cannot exist.
*/

$user = posix_getpwnam('www');
posix_setuid($user['uid']);
posix_setgid($user['gid']);
$pid = getmypid();

$GLOBALS['client'] = "classmated $argv[1]";

function sigint_handler() {
	global $sock, $argv, $fp, $mch;
	curl_multi_close($mch);
	fclose($fp);
	socket_close($sock);
	unlink("/private/tmp/classmate$argv[1]");
	db_close();
	Error::showSeparator();
	Error::setBgColour('#B66');
	Error::generate('debug', "Finished running classmated $argv[1] normally");
	Error::setBgColour('#555');
	Error::showSeparator();
}
//pcntl_signal(SIGINT, "sigint_handler");
register_shutdown_function('sigint_handler');

include("/Library/WebServer/Documents/includes/prepend.inc");
session_start();
db_connect();

include("$ROOT/includes/tags.inc");
include("$ROOT/includes/subjects.inc");
include("$ROOT/includes/universities.inc");
include("$ROOT/includes/geography.inc");

profiling_set_return_transfer(true);
profiling_start('all');

Error::showSeparator();
Error::setBgColour('#B66');
Error::generate('debug', "Loading classmated $argv[1]");
Error::setBgColour('#555');
Error::showSeparator();

$sock = socket_create(AF_UNIX, SOCK_STREAM, 0);
if(!$sock || !socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1)) {
	Error::generate('debug', "Error in classmate$argv[1]: ".socket_strerror(socket_last_error()));
	goto end;
}
$status = socket_bind($sock, "/private/tmp/classmate$argv[1].sock");
if(!$status) {
	Error::generate('debug', "Error in classmate$argv[1]: ".socket_strerror(socket_last_error()));
	goto end;
}
$status = socket_listen($sock, 100);
if(!$status) {
	Error::generate('debug', "Error in classmate$argv[1]: ".socket_strerror(socket_last_error()));
	goto end;
}
$clients = array();
$data = array();
$completed = array();
$lasttime = microtime();
$curl_stack = array();
$in_progress = array();
$mch = curl_multi_init();
$mcr = null;
$active = 0;
$locked = false;
$blocking = false; // set to false to potentially have multiple concurrent queries from 1 source
$max_simult_queries = 2; // max no. of simultaneous outgoing connections
// stats
$logpath = "$ROOT/admin/daemon_stats.log";
$fp = fopen($logpath, "a");
date_default_timezone_set('America/New_York');
$start_time = date(DATE_RFC822);
$n_reqs_recvd = 0;
$n_reqs_procd = 0;
$n_preempted_reqs = 0;
$time_delta = 1000*1000*1000;
$db_reconnect_delta = 0;
while(true) {
	$time_delta += profiling_end('all');
	profiling_start('all');
	if($time_delta >= 60*1000*1000) {
		fputs($fp,
		"\r\n".
		"Daemon ID     : $GLOBALS[client] v1 pid=$pid\r\n".
		"Start time    : $start_time\r\n".
		"Current time  : ".date(DATE_RFC822)."\r\n".
		"# reqs recvd  : $n_reqs_recvd\r\n".
		"# reqs procd  : $n_reqs_procd\r\n".
		"# preempted   : $n_preempted_reqs\r\n".
		"# in queue    : ".count($in_progress)."\r\n".
		"max simult    : $max_simult_queries\r\n".
		"IPC conns     : ".count($clients)."\r\n".
		"cURL actvconns: $active"."\r\n".
		profiling_print_summary()
		);
		$db_reconnect_delta += $time_delta;
		$time_delta = 0;
	}
	if($db_reconnect_delta > 3600*1000*1000) {
		db_close();
		db_connect();
		$db_reconnect_delta = 0;
	}
	$timeout = (count($completed)>0||count($in_progress)>0) ? 10000 : 1000000;
	if(count($completed)>0||count($in_progress)>0) $lasttime = microtime();
	$r = array_merge(array($sock), $clients);
	$w=$e=NULL;
	profiling_start('idle');
	if(socket_select($r, $w, $e, $timeout/1000000, $timeout%1000000) < 1) {
		profiling_end('idle');
		while(($mcr = curl_multi_exec($mch, $active)) === CURLM_CALL_MULTI_PERFORM) ;
		if(count($completed) > 0 || count($in_progress) > 0) {
			profiling_start('start of cycle');
			$info = curl_multi_info_read($mch);
			if($info) {
				$ch = $info['handle'];
				$top = '';
				foreach($in_progress as $k=>$v) {
					if($v['ch'] == $ch) {
						$top = $v['top'];
						unset($in_progress[$k]);
					}
				}
				$md5top = md5($top);
				$text = curl_multi_getcontent($ch);
				Error::disableLogging();
				db_query("REPLACE INTO primitive_cache (url, content) VALUES ('%s', '%s')", $md5top, $text);
				Error::enableLogging();
				db_end_transaction('primitive_cache', $md5top);
				curl_multi_remove_handle($mch, $ch);
				curl_close($ch);
				
				$n_reqs_procd++;
				fputs($fp, "Daemon $GLOBALS[client] processed $top\r\n");
				Error::generate('debug', "Processed $top");
			} else if(count($in_progress) < $max_simult_queries && count($curl_stack) > 0) {
				$ch = array_shift($curl_stack);
				$top = array_shift($completed);
				$md5top = md5($top);

				db_query("INSERT IGNORE INTO primitive_cache_lock (id,locked) VALUES ('%s','0')",$md5top);
				$exists = (db_affected_rows() == 0);
				// TODO: CURLOPT_TIMEVALUE and CURLOPT_TIMECONDITION on cache timestamp for Last-Modified if exists
				if(!$exists) {
					$locked = db_start_transaction('primitive_cache', $md5top, $blocking);
				} else {
					$locked = false;
				}
				
				if($locked) { // we've got a lock, we can either start or finish
					$in_progress[] = array('ch'=>$ch, 'top'=>$top);
					curl_multi_add_handle($mch, $ch);
					$mcr = curl_multi_exec($mch, $active);
					Error::generate('debug', 'starting');
				} else { // preempted
					// lock *was* acquired if blocking
					if($blocking) {
						db_end_transaction('primitive_cache', $md5top);
					}
					$n_preempted_reqs++;
				}
				profiling_end('start of cycle');
			}
		}
	} else {
		profiling_start('IPC');
		foreach($r as $s) {
			if($s == $sock) { // new connection
				if(!($newsock = socket_accept($sock))) {
					Error::generate('debug', "Error in classmate$argv[1]: ".socket_strerror(socket_last_error()));
				} else {
					$clients[intval($newsock)] = $newsock;
					$data[intval($newsock)] = '';
				}
			} else { // established connection
				if(($read = socket_read($s, 1024)) === false || $read == '') {
					if($read != '') {
						Error::generate('debug', "Error in classmate$argv[1]: ".socket_strerror(socket_last_error()));
					} else { // connection closed
					}
					unset($clients[intval($s)]);
				} else {
					if(strchr($read, ' ') === false) {
						$data[intval($s)] .= $read;
					} else {
						do{ 
							$data[intval($s)] .= $read;
							$read = '';
							$val = strtok($data[intval($s)], ' ');
							$data[intval($s)] = strtok(' ');
							array_push($completed, $val);
							$ch = curl_init($val);
							curl_setopt($ch, CURLOPT_AUTOREFERER, true);
							curl_setopt($ch, CURLOPT_FAILONERROR, true);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
							curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
							//curl_setopt($ch, CURLOPT_FORBID_REUSE, false);
							curl_setopt($ch, CURLOPT_FRESH_CONNECT, false);
							curl_setopt($ch, CURLOPT_STDERR, $logpath);
							// TODO: look into CURLOPT_RANGE
							array_push($curl_stack, $ch);
							Error::generate('debug', "Received: $val");
							fputs($fp, "Daemon $GLOBALS[client] received $val\r\n");
							$n_reqs_recvd++;
							if(!$data[intval($s)]) {
								$data[intval($s)] = '';
							}
						} while(strchr($data[intval($s)], ' '));
					}
				}
			}
		}
		profiling_end('IPC');
	}
}

end:
profiling_end('all');
sigint_handler(SIGINT);
