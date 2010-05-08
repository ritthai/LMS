<?php
$dbhost = 'localhost';
$dbuser = 'LMS_development';
$dbpass = '';
$dbname = 'LMS_development';

$h = mysql_connect($dbhost, $dbuser, $dbpass) or die("mysql_connect error: ".mysql_error());
mysql_select_db($dbname, $h) or die("mysql_select_db error: ".mysql_error());

mysql_query("CREATE DATABASE LMS_development;");
mysql_query("DROP TABLE courses;");
mysql_query("CREATE TABLE courses (id INT NOT NULL AUTO_INCREMENT, name VARCHAR(10), goog_res_tbl_id INT, youtube_res_tbl_id INT, timestamp TIMESTAMP(8) DEFAULT NOW(), PRIMARY KEY(id), UNIQUE(name));");
echo mysql_error();

mysql_close($h);
?>
