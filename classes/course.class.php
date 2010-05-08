<?php
class Course {
	private $goog_res_tbl_id, $youtube_res_tbl_id;
	private $name;
	private $prof;
	private $id;
	function __construct($_name, $_prof, $_goog_res, $_youtube_res) {
		$name = mysql_real_escape_string($_name);
		$prof = mysql_real_escape_string($_prof);
		$hash = md5($name.$prof);
		database_query("DROP TABLE goog_res_tbl_$hash");
		database_query("DROP TABLE youtube_res_tbl_$hash");
		database_query("CREATE TABLE goog_res_tbl_$hash (id INT, title VARCHAR(64), link VARCHAR(2048))");
		database_query("CREATE TABLE youtube_res_tbl_$hash (id INT, link VARCHAR(2048))");
	}
}
?>
