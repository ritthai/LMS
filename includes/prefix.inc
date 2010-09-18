<?php
function global_error_handler($error_level, $error_message, $error_file, $error_line) {
	Error::generate($error_level, "$error_message in $error_file on line $error_line");
}
set_error_handler("global_error_handler");
function controller_prefix() {
	$GLOBALS['client'] = $_SERVER['REMOTE_ADDR'];
	$GLOBALS['userid'] = User::GetAuthenticatedAttrib('name') or '-';
	Error::showSeparator();
	Error::setBgColour('#B66');
	Error::generate('debug', "Loading $_SERVER[REQUEST_URI]");
	if($tmp1 = User::GetAuthenticatedID()) Error::generate('debug', "Logged in as ".User::GetAttrib($tmp1, 'name'));
	Error::setBgColour('#555');
	Error::showSeparator();

	Error::generate('debug', 'Request URI: '.$_SERVER['REQUEST_URI']);
	if(isset($_SESSION['HTTP_USER_AGENT'])) {
		if($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT'])) {
			User::Deauthenticate();
			Error::generate('warn', 'Your session may have been compromised. Please login again.');
			Error::generate('suspicious', 'Your session may have been compromised. Please login again. session_id = '.session_id());
		}
	} else {
		$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
	}
}
function check_perms($action) {
	global $CONTROLLER, $HTMLROOT;
	if(is_bool($action)) {
		$decision = !$action;
	} else {
		$decision = !($action && $action->checkPerms());
	}
	if($decision) {
		if(User::IsAuthenticated()) {
			Error::generate('warn', 'Insufficient privileges to access page.');
			Error::generate('suspicious', 'Insufficient privileges to access page.');
			
			if($_SESSION['last_rendered_page']) {
				redirect_raw($_SESSION['last_rendered_page']);
			} else {
				redirect_raw($HTMLROOT);
			}
		} else {
			redirect('user', 'login');
		}
		return false;
	} else {
		return true;
	}
}
