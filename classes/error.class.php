<?php
class Error {
    static private $errors = array();
    static public $PRIORITY = array('fatal'=>10, 'warn'=>5, 'notice'=>0);
    function generate($priority, $error) {
		$fp = fopen('admin/debug.log', 'a');
		fwrite($fp, self::format_error(array('priority'=>$priority, 'msg'=>$error)));
		fclose($fp);
        switch($priority) {
        case self::$PRIORITY['fatal']:
            die($error);
            break;
        case self::$PRIORITY['warn']:
        case self::$PRIORITY['notice']:
            array_push(self::$errors, array('priority'=>$priority, 'msg'=>$error));
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
		return sprintf("%s [%d] %s\r\n", date(DATE_RFC822), $error[priority], $error[msg]);
	}
}
?>
