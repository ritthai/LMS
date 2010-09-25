<?php
function setDefaultText($defaultStr) {
	echo "value=\"$defaultStr\" ";
	echo "onClick=\"rmText(this,'$defaultStr');\" ";
	echo "onBlur=\"addText(this, '$defaultStr');\"";
}
function jsRegisterGlobal($key, $val) {
	echo "RegisteredGlobals[\"$key\"] = \"$val\";";
}
function jsSetupAutocompleteWithId($form_id, $textfield_id, $idtextfield_id, $word_list, $minchars=0) {
	global $args;
	$qstr = "/autocomplete?list=$word_list&";
	if(isset($args['country'])) {
		$c = $args['country']['id'];
		$qstr .= "country=$c&";
	}
	if(isset($args['area'])) {
		$c = $args['area']['id'];
		$qstr .= "area=$c&";
	}
	if(isset($args['university'])) {
		$c = $args['university']['id'];
		$qstr .= "university=$c&";
	}
	echo "<script type=\"text/javascript\"> <!--\r\n";
		echo "Event.observe(window, 'load', function(evt) {\r\n";
			echo "var options = {\r\n";
			echo "script: \"$qstr\",\r\n";
			echo "varname: \"val\",\r\n";
			echo "json: true,\r\n";
			echo "cache: false,\r\n";
			echo "delay: 0,\r\n";
			echo "timeout: 10000,\r\n";
			if($minchars > 0) echo "minchars: $minchars,\r\n";
			echo "callback: function(obj) { \$('$form_id').submit(); },\r\n";
			echo "maxresults: 22\r\n";
		echo "};\r\n";
		echo "var as$form_id = new bsn.AutoSuggest('$textfield_id', options);\r\n";
		echo "as$form_id.idtextfield = '$idtextfield_id'";
	echo "});\r\n";
	echo "--> </script>\r\n";
}
function jsSetupAutocomplete($form_id, $textfield_id, $word_list, $minchars=0) {
	global $args;
	$qstr		= "/autocomplete?list=$word_list&";
	if(isset($args['country'])) $qstr .= "country=$args[country]&";
	if(isset($args['area'])) $qstr .= "area=$args[area]&";
	if(isset($args['university'])) $qstr .= "university=$args[university]&";
	echo "<script type=\"text/javascript\"> <!--\r\n";
		echo "Event.observe(window, 'load', function(evt) {\r\n";
			echo "var options = {\r\n";
			echo "script: \"$qstr\",\r\n";
			echo "varname: \"val\",\r\n";
			echo "json: true,\r\n";
			echo "delay: 0,\r\n";
			echo "timeout: 10000,\r\n";
			echo "cache: false,\r\n";
			if($minchars > 0) echo "minchars: $minchars,\r\n";
			if($form_id == '') {
				echo "callback: function(obj) { },\r\n";
			} else {
				echo "callback: function(obj) { \$('$form_id').submit(); },\r\n";
			}
			echo "maxresults: 22\r\n";
		echo "};\r\n";
		echo "var as$form_id = new bsn.AutoSuggest('$textfield_id', options);\r\n";
	echo "});\r\n";
	echo "--> </script>\r\n";
}
function escapeForJS($str) {
	return str_replace(	array('\'', '"'),
						array('&#39;', '&#c4;'),
						$str );
}
function clean($str) {
	$str = str_replace("\\n", "<br/>", $str);
	$str = str_replace("\\'", "'", $str);
	$str = str_replace("'", "&apos;", $str);
	$str = str_replace("\\\"", "\"", $str);
	$str = str_replace("\"", "&quot;", $str);
	return $str;
}
function escape_js($str) {
	$str = str_replace("&apos;", "\'", $str);
	return $str;
}
function limit($str, $postfix, $n) {
	if(strlen($str) + strlen($postfix) > $n) {
		return substr($str, 0, $n - strlen($postfix)).'...'.$postfix;
	} else {
		return $str.$postfix;
	}
}
