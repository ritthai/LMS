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
?>
