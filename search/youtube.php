<?php
session_start();

$TERMS = urlencode($terms);
$TAGS = $tags;

$sub = subsets($TAGS);
foreach($sub as $s) {
	if(!count($sub)) continue;
	$r = youtube_query($TERMS, $s);
	if(count($r)) {
		$_SESSION['youtube'] = array_merge($_SESSION['youtube'], $r);
		break;
	}
}
?>
