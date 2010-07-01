<?php
class Error {
    static private $errors = array();
    static public $PRIORITY = array('fatal'=>10, 'warn'=>5, 'notice'=>1, 'debug'=>0, 'suspicious'=>-1);
    function generate($priority, $error) {
		global $CONFIG;
		global $ROOT;
		if(is_string($priority))
			$priority = self::$PRIORITY[$priority];
		if($CONFIG['debug'] === false && $priority==self::$PRIORITY['debug']) return;
		
		// format string
		$pname = 'notice';
		foreach(self::$PRIORITY as $k=>$v)
			if($v == $priority) {
				$pname = $k;
				break;
			}
		$fmt = "<div class=\"$pname\">%s</div>\r\n";
		
		// log it
		$fp = fopen($ROOT.'/admin/debug.html', 'a');
		fprintf($fp, $fmt, self::format_error(array('priority'=>$priority, 'msg'=>$error)));
		fclose($fp);
		
		// behaviour on error
        switch($priority) {
        case self::$PRIORITY['fatal']:
            die($error);
            break;
        case self::$PRIORITY['warn']:
        case self::$PRIORITY['notice']:
            array_push(self::$errors, array('priority'=>$priority, 'msg'=>$error));
            break;
        case self::$PRIORITY['debug']:
        case self::$PRIORITY['suspicious']:
			break;
		}
    }
    // Error with priority >= $priority
    function get($priority=0) {
		if(count(self::$errors) == 0)
			return null;
        $ret = array_filter(self::$errors,
                            function ($a) { return $a[priority] >= $priority; });
        self::$errors = array_filter(self::$errors,
                                function ($a) { return $a[priority] < $priority; });
        return $ret;
    }
	// Same formatting as in log
	function format_error($error) {
		date_default_timezone_set('America/New_York');
		return sprintf("%s [%d] %s", date(DATE_RFC822), $error[priority], $error[msg]);
	}
}
?>
