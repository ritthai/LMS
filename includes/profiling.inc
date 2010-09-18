<?php
$profiling_block_start = array();
$profiling_block_cumulative = array();
$profiling_return_transfer = false;
// so we don't have infinite recursion when profiling Error::generate
$in_profiling_code = false;
function profiling_start($block) {
	global	$profiling_block_start,
			$in_profiling_code;

	if($in_profiling_code) return;
	$in_profiling_code = true;
	$profiling_block_start[$block] = utime();
	$in_profiling_code = false;
}
function profiling_get_elapsed($block) {
	global $profiling_block_start;
	if(!isset($profiling_block_start[$block])) return -1;
	else return get_us_delta(utime(), $profiling_block_start[$block]);
}
function profiling_end($block, $outputted=0) {
	global	$profiling_block_start,
			$profiling_block_cumulative,
			$in_profiling_code,
			$profiling_return_transfer;

	if($in_profiling_code) return;
	$in_profiling_code = true;
	$time = profiling_get_elapsed($block);
	if($block !== 'error::generate') {
		$ftime = number_format($time);
		if(!$outputted != 0) $output = $block;
		else $output = $outputted;
		if(!$profiling_return_transfer) {
			Error::generate('debug', "'$output' took $ftime us");
		} else if($outputted !== 0) {
			Error::generate('debug', "'$output' took $ftime us");
		}
	}
	if(array_key_exists($block, $profiling_block_cumulative)) {
		$profiling_block_cumulative[$block] += $time;
	} else {
		$profiling_block_cumulative[$block] = $time;
	}
	$in_profiling_code = false;
	if($profiling_return_transfer) {
		return $time;
	}
}
function profiling_print_summary() {
	global $profiling_block_cumulative, $profiling_return_transfer;
	if($profiling_return_transfer) {
		$ret = "Profiling summary:\r\n";
	} else {
		Error::showSeparator();
		Error::generate('debug', "Profiling summary");
	}
	foreach($profiling_block_cumulative as $k => $v) {
		$ftime = number_format($v);
		if($profiling_return_transfer) $ret .= "$k: $ftime us\r\n";
		else Error::generate('debug', "$k: $ftime us");
	}
	if($profiling_return_transfer) return $ret;
}
function profiling_set_return_transfer($val) {
	global $profiling_return_transfer;
	$profiling_return_transfer = $val;
}
