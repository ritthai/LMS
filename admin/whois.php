<form action="whois.php" method="get">
	Domain name: <input type="text" name="a" />
	<input type="submit" />
</form>
<?php
		$a = escapeshellarg($_GET['a']);
		echo syscall("whois $a > $ROOT/admin/whois.txt");
        echo nl2br(file_get_contents("$ROOT/admin/whois.txt"));
?>
