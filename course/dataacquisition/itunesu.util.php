<?php
function prefetch_itunesu_search($procd_descr) {
	$TERMS = urlencode($procd_descr);
	$USERIP = $_SERVER['REMOTE_ADDR'];
	$NRESULTS = 4;
	
	profiling_start('prefetch_itunesu_search');

	ini_set('user_agent', 'iTunes/8.1');
	$url = "http://ax.search.itunes.apple.com/WebObjects/MZSearch.woa/wa/advancedSearch?descriptionTerm=$TERMS&media=iTunesU";
	async_cache_file($url, 3);

	profiling_start('prefetch_itunesu_search');
}
function itunesu_search($procd_descr) {
	$TERMS = urlencode($procd_descr);
	$USERIP = $_SERVER['REMOTE_ADDR'];
	$NRESULTS = 4;
	
	profiling_start('itunesu_search');

	ini_set('user_agent', 'iTunes/8.1');
	$url = "http://ax.search.itunes.apple.com/WebObjects/MZSearch.woa/wa/advancedSearch?descriptionTerm=$TERMS&media=iTunesU";
	$data = cached_file_get_contents($url);
	$parser = xml_parser_create();
	xml_parse_into_struct($parser, $data, $xml);
	xml_parser_free($parser);
	$store = array();
	foreach($xml as $key=>$elem) {
		if($elem['tag'] == 'GOTOURL' && $elem['level'] == 21 && $elem['type'] == 'open'
				&& count($store) < $NRESULTS) {
			array_push($store, array(
						'source'=>'itunesu',
						'url'=>$elem['attributes']['URL'],
						'title'=>$elem['attributes']['DRAGGINGNAME'],
						'art'=>$xml[$key+2]['attributes']['URL']));
		}
	}

	profiling_end('itunesu_search');

	return $store;
}
