<?php
session_start();

$TERMS = urlencode($terms);
$TAGS = $tags;

$sub = subsets($TAGS);
print_r($sub);
foreach($sub as $s) {
	if(!count($sub)) continue;
	$r = youtube_query($TERMS, $s);
	//print_r($r);
	if(count($r)) {
		//print_r($_SESSION['youtube']);
		$_SESSION['youtube'] = array_merge($_SESSION['youtube'], $r);
		break;
	}
}
?>
