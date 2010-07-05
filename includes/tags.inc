<?php
$TAGS = array(	'ACTSC'	=> 'actsci,actuarial science,statistics,stats' );
$FULLTAGS = array(	'ACTSC'	=> 'actsci,actuarial science,statistics,stats,finance,financial,accounting,economy,economics' );

function get_full_tags($crs) { global $FULLTAGS; return get_tags_aux($crs, $FULLTAGS); }
function get_tags($crs) { global $TAGS; return get_tags_aux($crs, $TAGS); }

function get_tags_aux($crs, $TAGS) {
	$ret = array();
	preg_match('/([a-zA-Z]*)([0-9]*)([a-zA-Z]*)/', $crs->code, $matches);
	$subject = $matches[1];
	if(array_key_exists($subject, $TAGS)) {
		$ret = explode(',',$TAGS[$subject]);
	}
	
	preg_match_all('/([A-Z][a-z]*)/', $crs->title, $matches);
	for($i=0; $i < count($matches[0]); $i++) {
		array_push($ret, strtolower($matches[0][$i]));
	}
	
	return implode(',',$ret);
}
?>
