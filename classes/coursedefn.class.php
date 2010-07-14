<?php
class CourseDefn {
	public $id;
	public $code;
	public $title;
	public $descr;
	public $cid; // comment thread id
	function __construct($code) {
		$this->id = $this->code = $this->title = $this->descr = false;
		if(is_int($code)) {
			$this->id = $code;
		} else {
			$this->code = $code;
		}
	}
	function save() {
		// if $this->id include it in query?
		db_query(	"REPLACE INTO coursedefns (code, title, descr, cid)
						VALUES ('%s', '%s', '%s', '%s')",
					$this->code, $this->title, $this->descr, $this->cid);
	}
	function load() {
		if($this->code) {
			$res = db_query("SELECT * FROM coursedefns WHERE code LIKE '%s'",
							$this->code);
		} else if($this->id) {
			$res = db_query("SELECT * FROM coursedefns WHERE id='%d'",
							$this->id);

		} else {
			Error::generate('debug', 'Not enough information to find course in CourseDefn::load().');
			return false;
		}
		if(!$res) {
			Error::generate('debug', 'CourseDefn->load: $res is null');
			return false;
		}
		$ret = db_get_assoc($res);
		if(!$ret) {
			Error::generate('debug', 'CourseDefn->load: $ret is null');
			return false;
		}
		$this->title = $ret['title'];
		$this->code = $ret['code'];
		$this->id = $ret['id'];
		$this->descr = $ret['descr'];
		$this->cid = $ret['cid'];
		
		return true;
	}
	static function ListAll() {
		$res = db_query("SELECT id,code,title,descr FROM coursedefns ORDER BY code");
		$ret = db_get_list_result($res);
		return $ret;
	}
}
?>
