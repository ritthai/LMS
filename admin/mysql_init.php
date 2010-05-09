<?php
include("../includes/mysql.inc");
include("../classes/coursedefn.class.php");

function replace_accents($string) 
{ 
  return str_replace( array('&', 'à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), array(' ', 'a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'), $string); 
} 

$h = mysql_connect($dbhost, $dbuser, $dbpass) or die("mysql_connect error: ".mysql_error());
mysql_select_db($dbname, $h) or die("mysql_select_db error: ".mysql_error());

mysql_query("CREATE DATABASE LMS_development;");
echo mysql_error();

mysql_query("DROP TABLE courses;");
mysql_query("CREATE TABLE courses (id INT NOT NULL AUTO_INCREMENT, name VARCHAR(10), prof VARCHAR(30), timestamp TIMESTAMP(8) DEFAULT NOW(), PRIMARY KEY(id));");
echo mysql_error();

mysql_query("DROP TABLE coursedefns;");
mysql_query("CREATE TABLE coursedefns (id INT NOT NULL AUTO_INCREMENT, code VARCHAR(10), title VARCHAR(60), descr VARCHAR(1000), timestamp TIMESTAMP(8) DEFAULT NOW(), PRIMARY KEY(id));");
echo mysql_error();

$courses = file_get_contents("../scraping/courses2.xml");

$parser = xml_parser_create();
xml_parse_into_struct($parser, $courses, $xml);
xml_parser_free($parser);

$code = "";
$title = "";
$descr = "";
foreach($xml as $a) {
	if($a['tag'] == "CODE")
		$code = $a['value'];
	else if($a['tag'] == "TITLE")
		$title = $a['value'];
	else if($a['tag'] == "DESCRIPTION") {
		$descr = $a['value'];
		$code = ltrim(rtrim($code));
		$title = ltrim(rtrim($title));
		$descr = ltrim(rtrim($descr));
		$cd = new CourseDefn(mysql_real_escape_string(htmlspecialchars($code)));
		$cd->title = mysql_real_escape_string(htmlspecialchars($title));
		$cd->descr = mysql_real_escape_string(htmlspecialchars($descr));
		$cd->save();
	}
}



mysql_close($h);
?>
