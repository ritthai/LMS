<?php
function wikipedia_process_term($term) {
	$pts = urldecode($term);
	$pts = explode(' of ', $pts, 2);
	$pts = explode(' and ', $pts[0], 2);
	$pts = $pts[0];
	$ret = urlencode(depluralize($pts));
	return $ret;
}
function wikipedia_url($TERMS) {
	//$url = "http://en.wikipedia.org/w/api.php?format=dbg&action=query&aplimit=1&apfrom=$TERMS&list=allpages";
	$url = "http://en.wikipedia.org/w/api.php?action=query&prop=info&inprop=url&redirects=1&format=dbg&titles=$TERMS";
	return $url;
}
function prefetch_wikipedia_search($procd_descr) {
	profiling_start('prefetch_wikipedia_search');

	$TERMS = wikipedia_process_term($procd_descr);
	$url = wikipedia_url($TERMS);
	async_cache_file($url, 2);

	profiling_end('prefetch_wikipedia_search');
}
function wikipedia_search($procd_descr) {
	profiling_start('wikipedia_search');

	$TERMS = wikipedia_process_term($procd_descr);
	$url = wikipedia_url($TERMS);
	$data = cached_file_get_contents($url);
	Error::setPrepend($data);
	Error::generate('debug', 'WIKIPEDIA');
	eval('$arr = '.$data.';');

	foreach($arr['query']['pages'] as $k=>$v) {
		$title = urlencode($v['title']);
		$url = urlencode($v['fullurl']);
		// https://dgl.cx/2008/10/wikipedia-summary-dns
		if($k == -1) { // not found
			return array();
		}
		break;
	}
	// this is probably a topic from a random unintended field like "frequency analysis (cryptoanalysis)"
	if(strlen($title) < strlen($TERMS) - 5 || strlen($title) > strlen($TERMS) + 5 || strrchr($title, '%28')) {
		return array();
	}
	$store = array(	'title'	=> urldecode(str_replace('+',' ',$title)),
					//'link'	=> "http://www.wikipedia.org/w/index.php?title=$title",
					'link'	=> urldecode($url),
					'source'=> 'wikipedia' );
	profiling_end('wikipedia_search');
	return array($store);
}
