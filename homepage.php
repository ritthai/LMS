<?php
session_start();

include("includes/mysql.inc");
include("classes/course.class.php");

database_connect();

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
Course name: <input type="text" id="course_name" name="course_name" /><br/>
Prof: <input type="text" id="course_prof" name="course_prof" /><br/>
<input type="submit" value="Save" />
</form>
<hr/>
<?php
$first_load = false;
if(isset($_POST['terms'])) {
	$terms = $_POST['terms'];
	eval("?>".file_get_contents("search/google.php"));
	eval("?>".file_get_contents("search/youtube.php"));
} else if(isset($_SESSION['youtube']) && isset($_SESSION['google']) &&
			isset($_POST['course_name']) && isset($_POST['course_prof'])) {
	$crs = new Course($_POST['course_name'], $_POST['course_prof'],
					$_SESSION['google'], $_SESSION['youtube']);
	$crs->save();
} else if(isset($_GET['course'])) {
	$crs = new Course($_GET['course'], $_GET['prof'], null, null);
	$crs->load();
	$_SESSION['google'] = $crs->goog_res;
	$_SESSION['youtube'] = $crs->youtube_res;
} else $first_load = true;

$courses = Course::ListCourses();
?>
<p>List of courses:<br/>
<?php foreach($courses as $course) { ?>
	<a href="homepage.php?course=<?php echo $course[1]; ?>&prof=<?php echo $course[2]; ?>">
		<?php echo $course[1].", ".$course[2].", ".$course[3]; ?><br/>
	</a>
<?php	} ?>
</p>

<?php
if(!$first_load) {
	eval("?>".file_get_contents("search/google.view.php"));
	echo "<hr/>";
	eval("?>".file_get_contents("search/youtube.view.php"));
}
?>

</body>
</html>

<?php
database_close();
?>
