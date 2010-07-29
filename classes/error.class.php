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
									'suspicious'=>	-1
									);
    static public $FLAGS = array(	'none'		=>	0,
									'single'	=>	1		// Only add if the error queue is empty
								);
    static public function generate($priority, $error, $flags='none') {
		global $CONFIG;
		global $ROOT;

		if(!self::$logging_enabled) return;

		if(is_string($priority))
			$priority = self::$PRIORITY[$priority];
		//if($CONFIG['debug'] === false && $priority==self::$PRIORITY['debug']) return;

		if(session_id() != "" && isset($_SESSION['errors']))
			self::$errors = $_SESSION['errors'];

		if($flags & self::$FLAGS['single'] && count(self::$errors) > 0) return;

		// format string
		$pname = 'notice';
		foreach(self::$PRIORITY as $k=>$v)
			if($v == $priority) {
				$pname = $k;
				break;
			}
		$bgcolour = self::$bgcolour;
		$fmt = "<div class=\"$pname\" style=\"background-color: $bgcolour\">%s</div>\r\n";
		
		// log it
		$fp = fopen($ROOT.'/admin/debug.html', 'a');
		if(is_array($error)){
			Error::generate($priority, '> array start');
			foreach($error as $err) {
				Error::generate($priority, $err);
			}
			Error::generate($priority, '< array end');
		} else {
			fprintf($fp, $fmt, self::format_error(array('priority'=>$priority, 'msg'=>$error)));
		}
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
        case self::$PRIORITY['debug']:
        case self::$PRIORITY['suspicious']:
			break;
		}
		
		if(session_id() != "")
			$_SESSION['errors'] = self::$errors;
    }
    // Error with priority >= $priority
    static public function get($priority=0) {
		if(session_id() != "" && isset($_SESSION['errors']))
			self::$errors = $_SESSION['errors'];
		
		if(count(self::$errors) == 0)
			return null;
        $ret = array_filter(self::$errors,
                        function ($a) { return $a[priority] >= $priority; });
        self::$errors = array_filter(self::$errors,
                        function ($a) { return $a[priority] < $priority; });
		
		if(session_id() != "")
			$_SESSION['errors'] = self::$errors;

        return $ret;
    }
	// Same formatting as in log
	static public function format_error($error) {
		global $CONFIG;
		if($CONFIG['debug']) {
			return sprintf("%d [%d] %s\r\n", profiling_get_elapsed('all'), $error['priority'], $error['msg']);
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
