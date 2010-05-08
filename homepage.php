<?php
session_start();

$PAGE_TITLE = "homepage.php";
?>
<html>
<body>
<form action="<?php echo $PAGE_TITLE; ?>" method="post">
Enter the search terms: <input type="text" id="terms" name="terms" />
<input type="submit" value="Find resources" />
</form>

<hr/>

<form action="<?php echo $PAGE_TITLE; ?>" method="post">
Course name: <input type="text" id="course_name" name="course_name" />
<input type="submit" value="Save" />
</form>

<?php
if(isset($_POST['terms'])) {
	$terms = $_POST['terms'];
	echo "<hr/>";
	eval("?>".file_get_contents("search/google.php"));
	echo "<hr/>";
	eval("?>".file_get_contents("search/youtube.php"));
	
} else if(isset($_SESSION['youtube']) && isset($_SESSION['google']) && isset($_POST['course_name'])) {
	print($_POST['course_name']);
	ourcei
}
?>

</body>
</html>
