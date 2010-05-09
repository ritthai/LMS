<?php
class CourseDefn {
	public $id;
	public $code;
	public $title;
	public $descr;
	function __construct($code) {
		$this->code = mysql_real_escape_string($code);
	}
	function save() {
		database_query(sprintf(
					"REPLACE INTO coursedefns (code, title, descr) VALUES ('%s','%s', '%s')",
					$this->code, $this->title, $this->descr));
	}
	function load() {
		$res = database_query(sprintf(
					"SELECT * FROM coursedefns WHERE code LIKE '%s'",
					$this->code));
		
		if(!$res) return false;
		$ret = mysql_result($res,0);
//		while($row = mysql_fetch_row($res))
//			print_r($row);
		mysql_free_result($res);
		$this->title = $ret[1];
		$this->descr = $ret[2];
		
		return true;
	}
	static function ListCourseDefns() {
		$results = database_query("SELECT (code,title,descr) FROM coursedefns");
		$ret = array();
		while($row = mysql_fetch_row($results))
			array_push($ret, $row);
		mysql_free_result($results);
		return $ret;
	}
}
?>
