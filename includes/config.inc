<?php
require_once "$ROOT/deps/spyc.php";

// This is an array so that functions can access all config data with one global call.
/*$CONFIG = array('debug'				=> true,
				'recaptcha_pubkey'	=> '6LerQrsSAAAAAK768_OCsBc8uf7KLlqofEVopSt1',
				'recaptcha_prikey'	=> '6LerQrsSAAAAAK0Z9QcFtZNGEnL1ARtBqOYptABp',
				'mailhide_pubkey'	=> '013lK5nSug0owYEdHGzuwFrA==',
				'mailhide_prikey'	=> '7e238da27498ccb66006d1dcf9ed629b',
				'dbhost'			=> 'localhost',
				'dbuser'			=> 'LMS_development',
				'dbpass'			=> '',
				'dbname'			=> 'LMS_development',
				'user_agent'		=> 'ClassMate/0.1',
			);*/
$CONFIG = Spyc::YAMLLoad("$ROOT/includes/config.yaml");
//ini_set('upload_max_filesize', '16M') or die('ini_set failed for upload_max_filesize'); // change in .htaccess
