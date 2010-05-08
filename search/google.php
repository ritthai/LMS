<?php
session_start();

$TERMS = urlencode($descr." Lecture Notes");
$USERIP = $_SERVER['REMOTE_ADDR'];

$url = "http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=$TERMS&userip=$USERIP&rsz=small";
$data = file_get_contents($url);
$arr = json_decode($data, true);
$results = $arr['responseData']['results'];
$store = array();
foreach($results as $elem) {
	$link = $elem['url'];
	$title = $elem['titleNoFormatting'];
	array_push($store, array($title, $link));
}
$_SESSION['google'] = array_merge($_SESSION['google'], $store);
?>
