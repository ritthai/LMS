<?php
/**
	Given:	$PAGE_TITLE
			$PAGE_REL_URL
			$COURSE
			$COURSES
			$G_RESULTS
			$YT_RESULTS
*/
?>
<html>
	<head>
		<title><?php echo $PAGE_TITLE; ?></title>
	</head>
	<body>
		<center><img src="images/logo.png" /></center>
		
		<form action="<?php echo $PAGE_REL_URL; ?>" method="post">
Enter the course code: <input type="text" id="terms" name="terms" value="<?php echo $COURSE['code']; ?>" /><br/>
<?php   if(isset($COURSE)) {  ?>
	Description: <textarea cols=40 rows=5 id="descr" name="descr" /><?php echo $COURSE['descr']; ?></textarea><br/>
	Tags (comma-delimited): <textarea cols=30 rows=3 id="tags" name="tags" /><?php echo $COURSE['tags']; ?></textarea><br/>
<?php   }   ?>
<input type="submit" value="Find resources" />
		</form>

<p>Saving temporarily disabled until I figure out how it fits in to the site's functionality.</p>
<?php   if(isset($COURSE) && false) {  ?>
		<hr/>
		
		<form action="<?php echo $PAGE_REL_URL; ?>" method="post">
<!--Course code: --><input type="hidden" id="terms" name="terms" value="<?php echo $COURSE['code']; ?>" />
Course name: <input type="text" id="save_name" name="save_name" value="<?php echo $COURSE['title']; ?>" /><br/>
<!--Prof: --><input type="hidden" id="course_prof" name="course_prof" value="" />
<input type="submit" value="Save" />
		</form>
		<hr/>
<?php   }   ?>

		<div style="float:right; width: 300px; border: 1px solid #000; margin: 10px; height: 600px">
			<p>
				<div style="margin-left: 2em">List of Courses:</div><br/>
<?php	foreach($COURSES as $course) { ?>
				<a href="<?php echo $PAGE_REL_URL; ?>?course=<?php echo urlencode($course['name']); ?>&prof=<?php echo urlencode($course['prof']); ?>">
					<?php echo urldecode($course['name']); ?><br/>
				</a>
<?php   } ?>
			</p>
		</div>
<?php
if(isset($COURSE)) {
    eval("?>".file_get_contents("search/google.view.php"));
    echo "<hr/>";
    eval("?>".file_get_contents("search/youtube.view.php"));
    echo "<hr/>";
    eval("?>".file_get_contents("search/itunesu.view.php"));
}
?>

</body>
</html>
