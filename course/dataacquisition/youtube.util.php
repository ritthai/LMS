<?php
function prefetch_youtube_query($TERMS, $srch, $crs) {
	global $CONFIG;

	profiling_start('prefetch_youtube_query');

	$TAGS = $srch;
	$rtags = get_full_tags($crs);
	$str = urlencode($TERMS);//."+".implode("+", $srch);
	$url = "http://gdata.youtube.com/feeds/api/videos?q=$str&orderby=relevance&start-index=1&max-results=2&v=2&format=5";
	async_cache_file($url, 2);

	profiling_end('prefetch_youtube_query');
}
function youtube_query($TERMS, $srch, $crs) {
	global $CONFIG;

	profiling_start('youtube_query');

	$TAGS = $srch;
	$rtags = get_full_tags($crs);
	$str = urlencode($TERMS);//."+".implode("+", $srch);
	$url = "http://gdata.youtube.com/feeds/api/videos?q=$str&orderby=relevance&start-index=1&max-results=2&v=2&format=5";
    $data = cached_file_get_contents($url);
    $parser = xml_parser_create();
    xml_parse_into_struct($parser, $data, $xml);
    xml_parser_free($parser);
    $store = array();
    foreach($xml as $elem) {
		switch($elem['tag']) {
		case 'MEDIA:THUMBNAIL':
			$content['thumbnail_url'] = $elem['attributes']['URL'];
			$content['thumbnail_width'] = $elem['attributes']['WIDTH'];
			$content['thumbnail_height'] = $elem['attributes']['HEIGHT'];
			break;
		case 'MEDIA:PLAYER':
			$content['link'] = $elem['attributes']['URL'];
			break;
		case 'CONTENT':
			if($elem['attributes']['TYPE'] == 'application/x-shockwave-flash') {
				$val = $elem['attributes']['SRC'];
			} else {
				continue 2;
			}
			$content['src'] = $val;
			break;
		case 'MEDIA:CATEGORY':
			if($val != 'Howto' && $val != 'Education' && $val != 'News' && $val != 'Tech') {
				continue 2;
			} else if($val == 'Education') {
				$content['rating'] += 5;
			}
			$val = $elem['value'];
			$content[strtolower($elem['tag'])] = $val;
			break;
		case 'CATEGORY':
			if($elem['attributes']['SCHEME'] != 'http://gdata.youtube.com/schemas/2007/categories.cat') {
				continue 2;
			} else {
				$val = $elem['attributes']['TERM'];
				if($val != 'Howto' && $val != 'Education' && $val != 'News' && $val != 'Tech') {
					continue 2;
				} else if($val == 'Education') {
					$content['rating'] += 5;
				}
				$content[strtolower($elem['tag'])] = $val;
			}
			break;
		case 'MEDIA:KEYWORDS':
			$val = $elem['value'];
			if(!$val) continue 2;
			if($rtags) {
				$matches = count_matches(explode(', ',$val), explode(',',$rtags));
			} else {
				$matches = false;
			}
			if($matches > 0) {
				$content[strtolower($elem['tag'])] = $val;
				$content['rating'] += 5;
			} else {
				$content['rating'] -= 5;
				continue 2;
			}
			break;
		case 'MEDIA:DESCRIPTION':
			// YT:RATING : attributes : NUMDISLIKES, NUMLIKES
			$val = $elem['value'];
			if($rtags) {
				$matches = count_matches(explode(' ',$val), explode(',',$rtags));
			} else {
				$matches = false;
			}
			if($matches && $matches > 0) {
				$content[strtolower($elem['tag'])] = $val;
				$content['rating'] += 2;
			} else {
				$content['rating'] -= 2; continue 2;
			}
			break;
		case 'TITLE':
			$val = $elem['value'];
			$content[strtolower($elem['tag'])] = $val;
			break;
		case 'ENTRY':
			if($elem['type'] == 'open') {
				$content = array(	'src'=>false, 'title'=>false, 'media:keywords'=>false,
									'media:description'=>false, 'category'=>false, 'rating'=>10,
									'thumbnail_url'=>false, 'thumbnail_width'=>false,
									'thumbnail_height'=>false, 'link'=>false, 'source'=>'youtube' );
			} else if($elem['type'] == 'close') {
				if($content['rating'] > 8) {
					if($CONFIG['debug']) $content['title'] .= ' - rating='.$content['rating'];
					array_push($store, $content);
				}
			}
		default:
		}
    }

	profiling_end('youtube_query');

    return $store;
}
function youtube_search($procd_descr, $tags, $crs) {
	$terms = $procd_descr;//urlencode($procd_descr);
	return youtube_query($terms, $tags, $crs);
	//return search_with_tags($terms, $tags, 'youtube_query', $crs);
}
function prefetch_youtube_search($procd_descr, $tags, $crs) {
	$terms = $procd_descr;//urlencode($procd_descr);
	return prefetch_youtube_query($terms, $tags, $crs);
}
?>
