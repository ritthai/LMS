<?php
include_once("../includes/mysql.inc");

$passarg = "";
if($CONFIG['dbpass'] != "") $passarg = ' -p'.$CONFIG['dbpass'];

echo syscall("/usr/local/mysql/bin/mysql -u$CONFIG[dbuser]$passarg -D$CONFIG[dbname] < $ROOT/admin/migrations/create_users.sql", $status);

?>
