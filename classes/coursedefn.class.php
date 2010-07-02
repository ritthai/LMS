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
		db_query(	"REPLACE INTO coursedefns (code, title, descr) VALUES ('%s','%s', '%s')",
					$this->code, $this->title, $this->descr);
	}
	function load() {
		$res = db_query("SELECT * FROM coursedefns WHERE code LIKE '%s'",
						$this->code);
		if(!$res) {
			Error::generate('warn', 'CourseDefn->load: $res is null');
			return false;
		}
		$ret = db_get_assoc($res);
		if(!$ret) {
			Error::generate('warn', 'CourseDefn->load: $ret is null');
			return false;
		}
		$this->title = $ret['title'];
		$this->descr = $ret['descr'];
		
		return true;
	}
	static function ListAll() {
		$res = db_query("SELECT code,title,descr FROM coursedefns ORDER BY code");
		$ret = db_get_list_result($res);
		return $ret;
	}
}
?>
