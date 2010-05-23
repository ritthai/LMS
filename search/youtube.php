<?php
function youtube_query($TERMS, $srch) {
	$str = $TERMS."+".implode("+", $srch);
	$url = "http://gdata.youtube.com/feeds/api/videos?q=$str&orderby=relevance&start-index=1&max-results=2&v=2";
    $data = file_get_contents($url);
    $parser = xml_parser_create();
    xml_parse_into_struct($parser, $data, $xml);
    xml_parser_free($parser);
    $store = array();
    foreach($xml as $elem) {
        //if($elem['tag'] == "MEDIA:THUMBNAIL") {
        //  echo "<img src=\"".$elem['attributes']['URL']."\" /><br/>";
        //}
        //print_r($elem);
        $content = "";
        if($elem['tag'] == "CONTENT") {
            $content = $elem['attributes']['SRC'];
            $content .= "(".$str.")";
            array_push($store, $content);
        }
    }
    return $store;
}
function youtube_search($procd_descr, $tags) {
	$terms = urlencode($procd_descr);
	return search_with_tags($terms, $tags, 'youtube_query');
}
?>
