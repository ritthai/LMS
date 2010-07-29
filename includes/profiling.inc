<?php
$profiling_block_start = array();
$profiling_block_cumulative = array();
function profiling_start($block) {
	global $profiling_block_start;
	$profiling_block_start[$block] = utime();
}
function profiling_get_elapsed($block) {
	global $profiling_block_start;
	return get_ms_delta(utime(), $profiling_block_start[$block]);
}
function profiling_end($block) {
	global $profiling_block_start, $profiling_block_cumulative;
	$time = profiling_get_elapsed($block);
	Error::generate('debug', "'$block' query took $time ms");
	if(array_key_exists($block, $profiling_block_cumulative)) {
		$profiling_block_cumulative[$block] += $time;
	} else {
		$profiling_block_cumulative[$block] = $time;
	}
}
function profiling_print_summary() {
	global $profiling_block_cumulative;
	Error::showSeparator();
	Error::generate('debug', "Profiling summary");
	foreach($profiling_block_cumulative as $k => $v) {
		Error::generate('debug', "$k: $v ms");
	}
}
