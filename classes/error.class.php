<?php
class Error {
    static private $errors			= array();
	static private $logging_enabled = true;
	static private $bgcolour		= "#555";
    static public $PRIORITY	= array('fatal'		=>	10,
									'warn'		=>	5,
									'notice'	=>	1,
									'success'	=>	1,
									'debug'		=>	0,
									'prod_debug'=>	-2,
									'suspicious'=>	-1,
									);
    static public $FLAGS = array(	'none'		=>	0,
									'single'	=>	1,		// Only add if the error queue is empty
									'override'	=>	2,		// Replace the error queue
								);
	static private $prepend='', $memstats='';
	static public function setMemStats($str) {
		global $CONFIG;
		if($CONFIG['debug'] === false) return;
		if(is_array($str)) {
			ob_start();
			var_dump($str);
			static::$memstats = '<pre>'.ob_get_contents().'</pre><br>';
			ob_end_clean();
		} else {
			static::$memstats = $str;
		}
	}
	static public function setPrepend($str) {
		global $CONFIG;
		if(!static::$logging_enabled) return;
		if($CONFIG['debug'] === false) return;
		if(is_array($str)) {
			ob_start();
			var_dump($str);
			static::$prepend = '<pre>'.ob_get_contents().'</pre><br>';
			ob_end_clean();
		} else {
			static::$prepend = $str;
		}
	}
	static private function _output($fp, $fmt, $priority, $error) {
		global $CONFIG;
		if($CONFIG['debug'] === true) {
			$popup_prepend = static::$prepend;
			$memstats = static::$memstats;
			if(is_array($error)) {
				ob_start();
				var_dump($error);
				$popup_prepend = '<pre>'.htmlspecialchars(ob_get_contents()).'</pre><br>';
				ob_end_clean();
				$error = 'Array of size '.count($error);
			}
			$rand = ''.uniqid(mt_rand());
			$str = sprintf($fmt, self::format_error(array('priority'=>$priority, 'msg'=>$error)));
			$bt = array_reverse(debug_backtrace());
			$sp = 0;
			$trace = "";
			foreach($bt as $k=>$v) {
				extract($v);
				$file = substr($file, 1+strrpos($file, '/'));
				if($file == 'error.class.php') continue;
				$trace .= str_repeat("&nbsp;", ++$sp);
				$trace .= "file=$file, line=$line, function=$function<br>";
			}
			date_default_timezone_set('America/New_York');
			$date = date(DATE_RFC822);
			$error = htmlspecialchars($error);
			fprintf($fp, '	<a href="#%s" class="group">%s</a>
								<div style="display:none">
									<div id="%s" class="fancybox">
										%s
									</div>
								</div>'."\r\n\r\n",
					$rand, $str, $rand,
					"<pre>$error</pre><br>$popup_prepend<br><br>$trace<br>$memstats<br>$date");
			static::$prepend = '';
		} else {
			fprintf($fp, $fmt, self::format_error(array('priority'=>$priority, 'msg'=>$error)));
		}
	}
	// Takes a int or a known string for $priority
	// Takes a string or an array for $error
    static public function generate($priority, $error, $flags='none') {
		global $CONFIG;
		global $ROOT;

		profiling_start('error::generate');

		$class = '';
		if(!self::$logging_enabled) goto end;//$class .= 'hidden';

		if(is_string($priority))
			$priority = self::$PRIORITY[$priority];
		if($CONFIG['debug'] === false && $priority==self::$PRIORITY['debug']) goto end;

		if(session_id() != "" && isset($_SESSION['errors']))
			self::$errors = $_SESSION['errors'];

		if($flags & self::$FLAGS['single'] && count(self::$errors) > 0) goto end;
		if($flags & self::$FLAGS['override'] && count(self::$errors) > 0) {
			self::$errors = array();
		}

		// format string
		$pname = 'notice';
		foreach(self::$PRIORITY as $k=>$v)
			if($v == $priority) {
				$pname = $k;
				break;
			}
		$bgcolour = self::$bgcolour;
		$fmt = "<div class=\"$pname $class\" style=\"background-color: $bgcolour\">%s</div>\r\n";
		
		// log it
		$fp = fopen($ROOT.'/admin/debug.html', 'a');
		static::_output($fp, $fmt, $priority, $error);
		fclose($fp);
		
		// behaviour on error
        switch($priority) {
        case self::$PRIORITY['fatal']:
            die($error);
            break;
        case self::$PRIORITY['warn']:
        case self::$PRIORITY['notice']:
		case self::$PRIORITY['success']:
            array_push(	self::$errors,
						array('priority'=>$priority, 'msg'=>$error));
            break;
        case self::$PRIORITY['prod_debug']:
        case self::$PRIORITY['suspicious']:
			$fp = fopen($ROOT.'/admin/debug_special.html', 'a');
			static::_output($fp, $fmt, $priority, $error);
			fclose($fp);
			break;
        case self::$PRIORITY['debug']:
			break;
		}
		
		if(session_id() != "")
			$_SESSION['errors'] = self::$errors;

end:
		profiling_end('error::generate');
    }
    // Error with priority >= $priority
    static public function get($priority=0) {
		if(session_id() != "" && isset($_SESSION['errors']))
			self::$errors = $_SESSION['errors'];
		
		if(count(self::$errors) == 0)
			return null;
        $ret = array_filter(self::$errors,
            function ($a) { global $priority; return $a['priority'] >= $priority; });
        self::$errors = array_filter(self::$errors,
            function ($a) { global $priority; return $a['priority'] < $priority; });
		
		if(session_id() != "")
			$_SESSION['errors'] = self::$errors;

        return $ret;
    }
	// Same formatting as in log
	static public function format_error($error) {
		global $CONFIG;
		if($CONFIG['debug']) {
			return sprintf("%s | %s > %s [%d] %s\r\n",
							$GLOBALS['client'],
							substr((string)get_viewer_id(), 0, 5),
							number_format(profiling_get_elapsed('all')),
							$error['priority'], $error['msg']);
		} else {
			return sprintf("%s\r\n", $error['msg']);
		}
	}
	static public function disableLogging() {
		self::$logging_enabled = false;
	}
	static public function enableLogging() {
		self::$logging_enabled = true;
	}
	static public function showSeparator() {
		static::generate('debug', '<hr/>');
	}
	static public function setBgColour($col) {
		self::$bgcolour = $col;
	}
}
?>
