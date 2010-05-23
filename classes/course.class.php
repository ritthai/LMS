<?php
class Course {
	private $goog_res_tbl_id, $youtube_res_tbl_id;
	public $name;
	public $prof;
	public $id;
	public $goog_res, $youtube_res;
	function __construct($_name, $_prof, $_goog_res, $_youtube_res) {
		$this->name = mysql_real_escape_string($_name);
		$this->prof = mysql_real_escape_string($_prof);
		$this->goog_res = $_goog_res;
		if(!$_goog_res) $this->goog_res = array();
		$this->youtube_res = $_youtube_res;
		if(!$_youtube_res) $this->youtube_res = array();
		$hash = md5($this->name.$this->prof);
	}
	function save() {
		$hash = md5($this->name.$this->prof);
		$res = database_query(sprintf(
			"SELECT COUNT(*) FROM courses WHERE name='%s' AND prof='%s'",
			$this->name, $this->prof));
		$res = mysql_result($res, 0);
		if($res < 1) {
			database_query(sprintf(
						"REPLACE INTO courses (name, prof) VALUES ('%s','%s')",
						$this->name, $this->prof));
		} else {
			database_query(sprintf(
				"UPDATE courses SET timestamp=NOW() WHERE name='%s' AND prof='%s'",
				$this->name, $this->prof));
		}
	ob_start();
		database_query("DROP TABLE goog_res_tbl_$hash");
		database_query("DROP TABLE youtube_res_tbl_$hash");
	ob_clean();
		database_query("CREATE TABLE goog_res_tbl_$hash (id INT, title VARCHAR(64), link VARCHAR(2048))");
		database_query("CREATE TABLE youtube_res_tbl_$hash (id INT, link VARCHAR(2048))");
		foreach($this->goog_res as $res)
			database_query(sprintf(
				"INSERT INTO goog_res_tbl_%s (title, link) VALUES ('%s','%s')",
				$hash, $res[0], $res[1]));
		foreach($this->youtube_res as $res)
			database_query(sprintf(
				"INSERT INTO youtube_res_tbl_%s (link) VALUES ('%s')",
				$hash, $res));
	}
	function load() {
		$hash = md5($this->name.$this->prof);
		$res = database_query(sprintf(
				"SELECT * FROM goog_res_tbl_%s", $hash)) or die(" -- invalid course");
		while($row = mysql_fetch_row($res))
			array_push($this->goog_res, array($row[1], $row[2]));
		$res = database_query(sprintf(
				"SELECT * FROM youtube_res_tbl_%s", $hash));
		while($row = mysql_fetch_row($res))
			array_push($this->youtube_res, $row[1]);
		mysql_free_result($res);
	}
	static function ListAll() {
		$results = database_query("SELECT name,prof FROM courses ORDER BY timestamp");
		$ret = array();
		while($row = mysql_fetch_row($results))
			array_push(	$ret,
						array(	"name" => $row[0],
								"prof" => $row[1]));
		mysql_free_result($results);
		return $ret;
	}
}
?>
