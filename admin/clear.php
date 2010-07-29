<?php
database_connect();
$res = mysql_list_tables($dbname);
while($row = mysql_fetch_row($res))
	database_query("DROP TABLE ".$row[0]);
?>
