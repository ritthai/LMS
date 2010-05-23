<?php
function khanacad_search($procd_descr) {
	global $CONFIG;
	$TERMS = urlencode($procd_descr);
	$URL = "scraping/khan.xml";
	$MATCH_LIMIT_TYPE = 'number'; // number | percent
	$NMATCHES = 4;
	$MATCHPCT = 10; // 10% of matches
	$QLIMIT = 1.1;
	
	$data = file_get_contents($URL);
	$parser = xml_parser_create();
	$status = xml_parse_into_struct($parser, $data, $xml);
	if($status == 0)
		echo xml_error_string(xml_get_error_code($parser));
	xml_parser_free($parser);
	
	$store = array();
	$terms = preg_split("/\s+|\+/", $TERMS, null, PREG_SPLIT_NO_EMPTY);
	foreach($xml as $key=>$elem)
		if($elem['tag'] == 'SUBJECT') {
			$t = $elem['attributes']['TITLE'];
			//$lt = count(preg_split("/\s+|\+/",$t));
			$lt = count($terms);
			$matches = count_matches($t, $terms);
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
	
	return $ret;
}
?>
