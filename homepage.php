<?php
$PAGE_TITLE = "homepage.php";
?>
<html>
<body>
<form action="<?php echo $PAGE_TITLE; ?>" method="post">
Enter the search terms: <input type="text" id="terms" name="terms" />
<input type="submit" value="Find resources" />
</form>

<?php
if(isset($_POST['terms'])) {
	$terms = $_POST['terms'];
	echo "<hr/>";
	eval("?>".file_get_contents("_google_search.php"));
	echo "<hr/>";
	eval("?>".file_get_contents("_youtube_search.php"));
}
?>

</body>
</html>
