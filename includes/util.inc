<?php
function redirect_raw($url) {
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
?>
