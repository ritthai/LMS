<?php
// http://www.sequencepublishing.com/academic.html
$FUNCTION_WORDS = array('a bit of','a couple of','a few','a good deal of','a good many','a great deal of','a great many','a lack of','a little','a little bit of','a majority of','a minority of','a number of','a plethora of','a quantity of','all','an amount of','another','any','both','certain','each','either','enough','few','fewer','heaps of','less','little','loads','lots','many','masses of','more','most','much','neither','no','none','numbers of','part','plenty of','quantities of','several','some','the lack of','the majority of','the minority of','the number of','the plethora of','the remainder of','the rest of','the whole','tons of','various','all','another','any','anybody','anyone','anything','both','each','each other','either','everybody','everyone','everything','few','he','her','hers','herself','him','himself','his','I','it','its','itself','many','me','mine','myself','neither','no_one','nobody','none','nothing','one','one another','other','ours','ourselves','several','she','some','somebody','someone','something','such','that','theirs','them','themselves','these','they','this','those','us','we','what','whatever','which','whichever','who','whoever','whom','whomever','whose','you','yours','yourself','yourselves','aboard','about','above','absent','according to','across','after','against','ahead','ahead of','all over','along','alongside','amid','amidst','among','amongst','anti','around','as','as of','as to','aside','astraddle','astride','at','away from','bar','barring','because of','before','behind','below','beneath','beside','besides','between','beyond','but','by','by the time of','circa','close by','close to','concerning','considering','despite','down','due to','during','except','except for','excepting','excluding','failing','following','for','for all','from','given','in','in between','in front of','in keeping with','in place of','in spite of','in view of','including','inside','instead of','into','less','like','minus','near','near to','next to','notwithstanding','of','off','on','on top of','onto','opposite','other than','out','out of','outside','over','past','pending','per','pertaining to','plus','regarding','respecting','round','save','saving','similar to','since','than','thanks to','through','throughout','thru','till',/*'to',*/'toward','towards','under','underneath','unlike','until','unto','up','up to','upon','versus','via','wanting','with','within','without','a','all','an','another','any','both','each','either','every','her','his','its','my','neither','no','other','our','per','some','that','the','their','these','this','those','whatever','whichever','your','accordingly','after','albeit','although',/*'and',*/'as','because','before','both','but','consequently','either','for','hence','however','if','neither','nevertheless','nor','once','or','since','so','than','that','then','thence','therefore',/*'tho'',*/'though','thus','till','unless','until','when','whenever','where','whereas','wherever','whether','while','whilst','yet','be able to','can','could','dare','had better','have to','may','might','must','need to','ought','ought to','shall','should','used to','will','would');
function count_matches($haystack, $needles) {
	$ret = 0;
	foreach($haystack as $li) {
		foreach($needles as $n) {
			if(abs(strcasecmp($li, $n)) == 0) {
				$ret++;
			}
		}
	}
	return $ret;
}
function subsets($arr) {
    $cnt=count($arr);
    $ret = array();
    for($i=1<<($cnt)-1; $i > 0; $i--) {
        $tmp = array();
        for($j=0; $j<$cnt; $j++)
            if(!($i^(1<<$j)))
                array_push($tmp, urlencode($arr[$j]));
        array_push($ret, $tmp);
    }
    return $ret;
}
function search_with_tags($terms, $tags, $cb, $args=null) {
	if(!is_callable($cb)) {
		Error::generate('debug', 'search_with_tags: invalid callback');
		return array();
	}
	$sub = subsets($tags);
	if(!count($sub)) {
		if($args)
			return call_user_func($cb, $terms, array(), $args);
		else
			return call_user_func($cb, $terms, array());
	}
	$ret = array();
	foreach($sub as $s) {
		$r = array();
		if($args)
			$r = call_user_func($cb, $terms, $s, $args);
		else
			$r = call_user_func($cb, $terms, $s);
		if(count($r)) {
			$ret = array_merge($ret, $r);
			break;
		}
	}
	return $ret;
}
function process_description($descr) {
	global $FUNCTION_WORDS;
	sort($FUNCTION_WORDS, SORT_STRING);
	//foreach($FUNCTION_WORDS as $key=>$fword)
	//	$FUNCTION_WORDS[$key] = strtolower($fword);
	
	// Remove begin
	$remove = array('/\. [A-Z][^.]*primarily to/',
					'/\([A-Za-z]* [A-Za-z]*\)/',
					'/Applications ?(of|to)?/',
					'/([Aa]n)? ?[Ii]ntroduction ?(to)?/'
					);
	foreach($remove as $pattern) {
		$chunks = preg_split($pattern, $descr, null, PREG_SPLIT_NO_EMPTY);
		if($chunks && count($chunks) > 1) $descr = $chunks[0].' '.$chunks[1];
	}

	$chunks = preg_split('/- and /', $descr, null, PREG_SPLIT_NO_EMPTY);
	if($chunks && count($chunks) > 1) $descr = $chunks[0].'- & '.$chunks[1];

	if($c=preg_match_all('/(\w+) (\w+) (and) /', $descr, $matches, PREG_OFFSET_CAPTURE)) {
		//$shift = 0;
		for($i=0; $i<$c; $i++) {
			$offset = $matches[3][$i][1] + $shift;
			$descr[$offset] = $descr[$offset+2] = ' ';
			$descr[$offset+1] = ',';
			//$a = substr($descr, 0, $offset-1);
			//$b = substr($descr, $offset+4);
			//$descr = $a.', '.$b;
			//$shift += 3;
		}
	}
	// Remove end

	/* Do not require following and trailing spaces */
	$pdelimiters = array(	', and ', ',', '\.', ':', ';', '!', '\\?', '\\(', '\\)',
							'examples?');
	/* Require following and trailing spaces */
	$wdelimiters = array(	/*'and',*/ 'between', 'to', 'include', 'including', 'Introduction to',
							'[Tt]echniques( for)?', '[Tt]echniques to (monitor|calculate)' );
	foreach($pdelimiters as $key=>$val)
		$pdelimiters[$key] = $val;
	foreach($wdelimiters as $key=>$val)
		$wdelimiters[$key] = '\s+'.$val.'\s+';
	$chunks = preg_split('/'.implode('|',$pdelimiters).'|'
							.implode('|',$wdelimiters).'/',
							$descr, null, PREG_SPLIT_NO_EMPTY);
	$chunks2 = array();
	foreach($chunks as $key=>$chunk) {
		if($key==0) continue;
		$line = ereg_replace("^[a-z]?[a-z]?$", "", $chunk);
		$words = preg_split('/ /', $line, 0, PREG_SPLIT_NO_EMPTY);
		$left = array();
		foreach($words as $word) {
			$word = ereg_replace("[() ]", "", $word);
			if(!array_search($word, $FUNCTION_WORDS))
				array_push($left, $word);
		}
		array_push($chunks2, htmlspecialchars(implode(" ", $left), ENT_COMPAT, 'UTF-8', false));
	}
	return $chunks2;
}
function cached_file_update($url) {
	Error::generate('debug', "cached_file_update $url");
	$text = file_get_contents($url);
	db_query(	"REPLACE INTO primitive_cache (url, content) VALUES ('%s', '%s')",
				$url, $text );
	return $text;
}
function cached_file_get_contents($url) {
	Error::disableLogging();
	Error::generate('debug', "cached_file_get_contents $url");
	$res = db_query("SELECT * FROM primitive_cache WHERE url='%s'",
					$url );
	if(!$res)
		return cached_file_update($url);
	$ret = mysql_fetch_row($res);
	if(!$ret)
		return cached_file_update($url);
	mysql_free_result($res);
	Error::enableLogging();
	return $ret[1];
}
function utime() {
	$time = explode(' ', microtime());
	$usec = (double)$time[0];
	$sec  = (double)$time[1];
	return $sec+$usec;
}
function get_ms_delta($end, $start) {
	return (int)(($end - $start) * 1000);
}
?>
