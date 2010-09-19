<?php
function redirect_raw($url) {
	global $HTMLROOT;
	if(!$url) return false;
	if($url[0] == '/') $url = "$HTMLROOT$url";
	//header("Status: 200");
	header('Cache-Control: max-age=0, no-cache, no-store, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header("Location: $url", true, 303);
	Error::generate('debug', "Redirecting to $url");
	die('Redirecting to '.$url);
    // watch out, execution continues after a call to header("Location: ...")
}
function redirect(/*$controller='user', $action=''*/) {
    global $HTMLROOT, $CONTROLLER;
    $controller = $CONTROLLER;
    $action = '';
    if(func_num_args() == 1) $action = func_get_arg(0);
    if(func_num_args() == 2) {
        $action = func_get_arg(1);
        $controller = func_get_arg(0);
    }
	//header("Status: 200");
	header('Cache-Control: max-age=0, no-cache, no-store, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    if($controller == '') {
		$loc = "Location: $HTMLROOT/$action";
	} else {
		$loc = "Location: $HTMLROOT/$controller/$action";
	}
	header($loc, true, 303);
	Error::generate('debug', "Redirecting to $loc");
	die('Redirecting to '.$loc);
    // watch out, execution continues after a call to header("Location: ...")
}

function syscall($command) {
	$result = '';
    if ($proc = popen("($command)2>&1","r")) {
        while (!feof($proc)) $result .= fgets($proc, 1000);
        pclose($proc);
        return $result;
    }
}
function recursive_merge_reduce($arr) {
	if(!$arr) return false;
	while($arr[0] && is_array($arr[0])) {
		$arr = array_reduce($arr, 'array_merge', array());
	}
	return $arr;
}
function showYouTubeVid($content) {
	//this.href.replace(new RegExp(\"watch\\?v=\", \"i\"), 'v/'),
	echo <<<EOT
		jQuery.fancybox({
			'padding'		: 0,
			'autoScale'		: false,
			'transitionIn'	: 'none',
			'transitionOut'	: 'none',
			'title'			: this.title,
			'width'			: 680,
			'height'		: 495,
			'href'			: '$content',
			'type'			: 'swf',
			'swf'			: {
				'wmode'				: 'transparent',
				'allowfullscreen'	: 'false'
			}
		});
		return false;
EOT;
}
?>
