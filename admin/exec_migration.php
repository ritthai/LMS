<?php
include("../includes/mysql.inc");

function syscall($command) {
	if ($proc = popen("($command)2>&1","r")) {
		while (!feof($proc)) $result .= fgets($proc, 1000);
		pclose($proc);
		return $result; 
	}
}

$passarg = "";
if($CONFIG['dbpass'] != "") $passarg = ' -p'.$CONFIG['dbpass'];

echo syscall("/usr/local/mysql/bin/mysql -u$CONFIG[dbuser]$passarg -D$CONFIG[dbname] < $ROOT/admin/migrations/create_users.sql", $status);

?>
