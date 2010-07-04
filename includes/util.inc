<?php
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
    header("Location: $HTMLROOT/$controller/$action", true, 303);
    // watch out, execution continues after a call to header("Location: ...")
}
?>
