<?php
profiling_start('all');

include("../classes/coursedefn.class.php");
include("../includes/universities.inc");
include("../includes/geography.inc");

function replace_accents($string) 
{ 
  return str_replace( array('&', 'à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), array(' ', 'a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'), $string); 
} 

$dbuser = $CONFIG['dbuser'];
$dbpass = $CONFIG['dbpass'];
$dbhost = $CONFIG['dbhost'];
$dbname = $CONFIG['dbname'];

profiling_start('run migrations');
include("exec_migration.php");
profiling_end('run migrations');

$h = mysql_connect($dbhost, $dbuser, $dbpass) or die("mysql_connect error: ".mysql_error());
mysql_select_db($dbname, $h) or die("mysql_select_db error: ".mysql_error());

//mysql_query("CREATE DATABASE LMS_development;");
//echo mysql_error();

profiling_start('create mysql_init tables');
mysql_query("DROP TABLE courses;");
mysql_query("CREATE TABLE courses (id INT NOT NULL AUTO_INCREMENT, name VARCHAR(40), prof VARCHAR(30), timestamp TIMESTAMP(8) DEFAULT NOW(), PRIMARY KEY(id));");
echo mysql_error();

mysql_query("DROP TABLE coursedefns;");
mysql_query("CREATE TABLE coursedefns
				(	id			INT NOT NULL AUTO_INCREMENT,
					code		VARCHAR(10),
					title		VARCHAR(60),
					descr		VARCHAR(1000),
					cid			INT,
					university	INT,
					timestamp	TIMESTAMP(8) DEFAULT NOW(),
					PRIMARY KEY(id));");
echo mysql_error();

mysql_query("DROP TABLE primitive_cache;");
mysql_query("CREATE TABLE primitive_cache
					(	url			VARCHAR(255),
						content		TEXT,
						timestamp	TIMESTAMP(8) DEFAULT NOW(),
						PRIMARY KEY(url));");
echo mysql_error();

mysql_query("DROP TABLE universities;");
mysql_query("CREATE TABLE universities
					(	id			INT NOT NULL AUTO_INCREMENT,
						name		TEXT,
						area		INT NOT NULL,
						PRIMARY KEY(id));");
echo mysql_error();
mysql_query("DROP TABLE areas;");
mysql_query("CREATE TABLE areas
					(	id			INT NOT NULL AUTO_INCREMENT,
						name		TEXT,
						country		INT NOT NULL,
						PRIMARY KEY(id));");
mysql_query("DROP TABLE countries;");
mysql_query("CREATE TABLE countries
					(	id			INT NOT NULL AUTO_INCREMENT,
						name		TEXT,
						PRIMARY KEY(id));");
echo mysql_error();
profiling_start('create mysql_init tables');

$courses		= file_get_contents("../scraping/courses2.xml");
$countries		= file_get_contents("../scraping/countries.xml");

profiling_start('parse course and country xml');
$parser = xml_parser_create();
xml_parse_into_struct($parser, $courses, $xml);
xml_parser_free($parser);
$parser = xml_parser_create();
xml_parse_into_struct($parser, $countries, $countries_xml);
xml_parser_free($parser);
profiling_end('parse course and country xml');

profiling_start('create countries');
foreach($countries_xml as $a) {
	if($a['tag'] == 'COUNTRY') {
		Country::Create($a['value']);
	}
}
profiling_end('create countries');

$country_id = Country::GetID('Canada');
profiling_start('create regions');
foreach($areas['Canada'] as $a) {
	Area::Create($a, $country_id);
}
profiling_end('create regions');

$area_id = Area::GetID('Ontario', $country_id);
profiling_start('create universities');
foreach($universities['Canada']['Ontario'] as $uni) {
	University::Create($uni, $area_id);
}
profiling_end('create universities');

$code = "";
$title = "";
$descr = "";
$uni_id = University::GetID('University of Waterloo');
profiling_start('create courses');
foreach($xml as $a) {
	if($a['tag'] == "CODE") {
		$code = $a['value'];
	} else if($a['tag'] == "TITLE") {
		$title = $a['value'];
	} else if($a['tag'] == "DESCRIPTION") {
		$descr = $a['value'];
		$code = ltrim(rtrim($code));
		$title = ltrim(rtrim($title));
		$descr = ltrim(rtrim($descr));
		$cid = Comment::Create(array(	'subject'	=> $code,
										'id'		=> 1 ));
		$cd = new CourseDefn(mysql_real_escape_string(htmlspecialchars($code)));
		$cd->title = mysql_real_escape_string(htmlspecialchars($title));
		$cd->descr = mysql_real_escape_string(htmlspecialchars($descr));
		$cd->cid = $cid;
		$cd->university = $uni_id;
		$cd->save();
	}
}
profiling_end('create courses');

mysql_close($h);

profiling_end('all');
profiling_print_summary();
?>
