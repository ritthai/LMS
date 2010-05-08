<?php
session_start();

$TERMS = urlencode($terms);

$url = "http://gdata.youtube.com/feeds/api/videos?q=$TERMS&orderby=relevance&start-index=1&max-results=10&v=2";
$data = file_get_contents($url);
$parser = xml_parser_create();
xml_parse_into_struct($parser, $data, $xml);
xml_parser_free($parser);
$store = array();
foreach($xml as $elem) {
	//if($elem['tag'] == "MEDIA:THUMBNAIL") {
	//	echo "<img src=\"".$elem['attributes']['URL']."\" /><br/>";
	//}
	//print_r($elem);
	$content = "";
	if($elem['tag'] == "CONTENT") {
		$content = $elem['attributes']['SRC'];
		array_push($store, $content);
	}
	$_SESSION['youtube'] = $store;
}
?>
