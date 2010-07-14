<?php
function controller_prefix() {
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
?>
