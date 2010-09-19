<?php
function wikipedia_process_term($term) {
	$pts = urldecode($term);
	$pts = explode('of', $pts, 2);
	$pts = explode('and', $pts[0], 2);
	//$pts = str_replace('&amp;', '&', $pts[0]);
	$pts = $pts[0];
	/*if($pts[strlen($pts)-2] != 'e') {
		$ret = rtrim(urlencode($pts), 's');
	} else {
		$ret = urlencode($pts);
	}*/
	$ret = urlencode(depluralize($pts));
	return $ret;
}
function prefetch_wikipedia_search($procd_descr) {
	profiling_start('prefetch_wikipedia_search');

	$TERMS = wikipedia_process_term($procd_descr);
	$url = "http://en.wikipedia.org/w/api.php?format=dbg&action=query&aplimit=1&apfrom=$TERMS&list=allpages";
	async_cache_file($url, 2);

	profiling_end('prefetch_wikipedia_search');
}
function wikipedia_search($procd_descr) {
	profiling_start('wikipedia_search');

	$TERMS = wikipedia_process_term($procd_descr);
	
	$url = "http://en.wikipedia.org/w/api.php?format=dbg&action=query&aplimit=1&apfrom=$TERMS&list=allpages";
	$data = cached_file_get_contents($url);
	eval('$arr = '.$data.';');

	$title = urlencode($arr['query']['allpages'][0]['title']);
	// this is probably a topic from a random unintended field like "frequency analysis (cryptoanalysis)"
	if(strlen($title) < strlen($TERMS) - 5 || strlen($title) > strlen($TERMS) + 5 || strrchr($title, '%28')) {
		return array();
	}
	$store = array(	'title'	=> urldecode(str_replace('+',' ',$title)),
					'link'	=> "http://www.wikipedia.org/w/index.php?title=$title",
					'source'=> 'wikipedia' );
	profiling_end('wikipedia_search');
	return array($store);
}
