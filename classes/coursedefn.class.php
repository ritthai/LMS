<?php
class CourseDefn {
	public $id;
	public $code;
	public $title;
	public $descr;
	public $cid; // comment thread id
	public $university;
	function __construct($code) {
		$this->id = $this->code = $this->title = $this->descr = false;
		if(is_int($code)) {
			$this->id = $code;
		} else {
			$this->title = $this->code = $code;
		}
	}
	function save() {
		// if $this->id include it in query?
		db_query(	"REPLACE INTO coursedefns (code, title, descr, cid, university)
						VALUES ('%s', '%s', '%s', '%s', '%d')",
					$this->code, $this->title, $this->descr, $this->cid, $this->university);
	}
	function load() {
		if($this->code) {
			$res = db_query("SELECT * FROM coursedefns WHERE STRCMP(code,'%s')=0",
							$this->code);
		} else if($this->id) {
			$res = db_query("SELECT * FROM coursedefns WHERE id='%d'",
							$this->id);
		} else if($this->title) {
			$res = db_query("SELECT * FROM coursedefns WHERE STRCMP(title,'%s')=0",
							$this->title);
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
		$this->university = $ret['university'];
		
		return true;
	}
	static function ListAll($uni=false) {
		if($uni) {
			$uni = mysql_real_escape_string($uni);
			$constraint = "WHERE university=$uni";
		} else {
			$constraint = '';
		}
		$res = db_query("SELECT id,code,title,descr,university
							FROM coursedefns
							$constraint
							ORDER BY title");
		$ret = db_get_list_of_assoc($res);
		return $ret;
	}
	static function ListAllWithCode($code, $uni=false) {
		if($uni) {
			$uni = mysql_real_escape_string($uni);
			$constraint = "AND university=$uni";
		} else {
			$constraint = '';
		}
		$res = db_query("
			SELECT id,code,title,descr,university
				FROM		coursedefns
				WHERE		code REGEXP '^%s[0-9]+'
							$constraint
				ORDER BY	code",
			$code);
		$ret = db_get_list_of_assoc($res);
		return $ret;
	}
	static function ListAllStartingWithTitle($title, $orderby='title', $uni=false) {
		if($uni) {
			$uni = mysql_real_escape_string($uni);
			$constraint = "AND university=$uni";
		} else {
			$constraint = '';
		}
		$res = db_query("
			SELECT id,code,title,descr,university
				FROM		coursedefns
				WHERE		title REGEXP '^%s.*'
							$constraint
				ORDER BY	$orderby",
			$title);
		$ret = db_get_list_of_assoc($res);
		return $ret;
	}
	static function ListAllContainingTitle($title, $orderby='title', $uni=false) {
		if($uni) {
			$uni = mysql_real_escape_string($uni);
			$constraint = "AND university=$uni";
		} else {
			$constraint = '';
		}
		$res = db_query("
			SELECT id,code,title,descr,university
				FROM		coursedefns
				WHERE		title REGEXP '.*%s.*'
							$constraint
				ORDER BY	$orderby",
			$title);
		$ret = db_get_list_of_assoc($res);
		return $ret;
	}
	static function ListAllStartingWithCode($code, $orderby='code', $uni=false) {
		if($uni) {
			$uni = mysql_real_escape_string($uni);
			$constraint = "AND university=$uni";
		} else {
			$constraint = '';
		}
		$res = db_query("
			SELECT id,code,title,descr,university
				FROM		coursedefns
				WHERE		code REGEXP '^%s.*'
							$constraint
				ORDER BY	$orderby",
			str_replace(' ', '', $code));
		$ret = db_get_list_of_assoc($res);
		return $ret;
	}
}
