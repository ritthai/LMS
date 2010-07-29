<?php
function google_search($procd_descr) {
	profiling_start('google_search');

	$TERMS = urlencode($procd_descr." Lecture Notes");
	$USERIP = $_SERVER['REMOTE_ADDR'];
	
	$url = "http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=$TERMS&rsz=small";
	$data = cached_file_get_contents($url);
	$arr = json_decode($data, true);
	$results = $arr['responseData']['results'];
	$store = array();
	foreach($results as $elem) {
		$link = $elem['url'];
		$title = $elem['titleNoFormatting'];
		array_push($store, array($title, $link));
	}

	profiling_end('google_search');
	return $store;
}
?>
