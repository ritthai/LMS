<?php
function khanacad_query($terms, $tags, $xml) {
	global $CONFIG;

	profiling_start('khanacad_query');

	$MATCH_LIMIT_TYPE = 'number'; // number | percent
	$NMATCHES = 4;
	$MATCHPCT = 10; // 10% of matches
	$QLIMIT = 1.1;
	$terms = array_merge($terms, $tags);
	$TERMS = implode(' ', $terms);
	$store = array();
	foreach($xml as $key=>$elem)
		if($elem['tag'] == 'SUBJECT') {
			$t = $elem['attributes']['TITLE'];
			//$lt = count(preg_split("/\s+|\+/",$t));
			$ltitle = count($t);
			$lterms = count($terms);
			$matches = count_matches(explode(' ',$t), $terms);
			array_push($store, array(
						'matches'=>($matches),
						'title'=>$t,
						'url'=>$elem['attributes']['LINK']));
		}
	usort($store, function ($a,$b) { return $a['matches']>$b['matches']?-1:1; } );
	
	$ret = array();
	//for($i=0; $i<min($NMATCHES,count($store)); $i++)
	//	array_push($ret, array('title'=>$store[$i]['title'], 'url'=>$store[$i]['url']));
	foreach($store as $s)
		if( (($MATCH_LIMIT_TYPE == 'number' && count($ret) < $NMATCHES)
			|| ($MATCH_LIMIT_TYPE == 'percent' && count($ret)/count($store) < $MATCHPCT/100))
			&& $s['matches'] >= $QLIMIT)
		{
			$title = $s['title'];
			if($CONFIG['debug']) $title .= "<b> -- quality: ".$s['matches']." terms: ".$TERMS."</b>";
			array_push($ret, array('title'=>$title, 'url'=>$s['url']));
		}
	usort($ret, function ($a,$b) { return strnatcasecmp($a['title'],$b['title']); } );

	profiling_end('khanacad_query');
	
	return $ret;
}
function khanacad_search($procd_descr, $tags=array()) {
	global $CONFIG;
	global $ROOT;

	profiling_start('khanacad_search');

	$TERMS = urlencode($procd_descr);
	$URL = "$ROOT/scraping/khan.xml";
	
	$data = file_get_contents($URL);
	$parser = xml_parser_create();
	$status = xml_parse_into_struct($parser, $data, $xml);
	if($status == 0)
		echo xml_error_string(xml_get_error_code($parser));
	xml_parser_free($parser);
	
	$terms = preg_split("/\s+|\+/", $TERMS, null, PREG_SPLIT_NO_EMPTY);

	$ret = search_with_tags($terms, $tags, 'khanacad_query', $xml);

	profiling_end('khanacad_search');

	return $ret;
}
?>
