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
		$ret = mysql_fetch_row($res);
		mysql_free_result($res);
		$this->title = $ret[2];
		$this->descr = $ret[3];
		
		return true;
	}
	static function ListCourseDefns() {
		$results = database_query("SELECT code,title,descr FROM coursedefns ORDER BY code");
		$ret = array();
		while($row = mysql_fetch_row($results))
			array_push(	$ret,
						array(	"code" => $row[0],
								"title" => $row[1],
								"descr" => $row[2]));
		mysql_free_result($results);
		return $ret;
	}
}
?>
