<?php
/**
	Given:	pagetitle,
			pageurl,
			course:	title,
					code,
					descr
			courses,
			searchresults,
			actions
*/
?>
<html>
	<head>
		<title><?php echo $args['pagetitle']; ?></title>
	</head>
	<body>
<?php	if($errors=Error::get()) {	?>
		<div style="border: 1px solid #F00; background-color: #fff5f5;">
			<ul>
			<?php foreach($errors as $error) {	?>
				<li><?php echo Error::format_error($error); ?></li>
			<?php }	?>
			</ul>
		</div>
<?php	}	?>
		<center><img src="<?php echo $HTMLROOT; ?>/images/logo.png" /></center>
		
<?php $args['actions']['search']->FORM_BEGIN(); ?>
	Enter the course code: <input type="text" id="terms" name="terms" value="<?php echo $args['course']['code']; ?>" /><br/>
	Tags (comma-delimited): <textarea cols=30 rows=3 id="tags" name="tags" /><?php echo $args['course']['tags']; ?></textarea><br/>
<input type="submit" value="Find resources" />
<?php $args['actions']['search']->FORM_END(); ?>

<?php   if($args['actions']['search']->wasCalled()) {  ?>
	Description: <textarea cols=40 rows=5 id="descr" name="descr" /><?php echo $args['course']['descr']; ?></textarea><br/>
<?		}	?>

<p>Saving temporarily disabled until I figure out how it fits in to the site's functionality.</p>
<?php   if(false) {  ?>
		<hr/>
		
		<form action="<?php echo $PAGE_REL_URL; ?>" method="post">
<!--Course code: --><input type="hidden" id="terms" name="terms" value="<?php echo $COURSE['code']; ?>" />
Course name: <input type="text" id="save_name" name="save_name" value="<?php echo $COURSE['title']; ?>" /><br/>
<!--Prof: --><input type="hidden" id="course_prof" name="course_prof" value="" />
<input type="submit" value="Save" />
		</form>
		<hr/>
		<div style="float:right; width: 300px; border: 1px solid #000; margin: 10px; height: 600px">
			<p>
				<div style="margin-left: 2em">List of Courses:</div><br/>
<?php	/*foreach($COURSES as $course) { ?>
				<a href="<?php echo $PAGE_REL_URL; ?>?course=<?php echo urlencode($course['name']); ?>&prof=<?php echo urlencode($course['prof']); ?>">
					<?php echo urldecode($course['name']); ?><br/>
				</a>
<?php   }*/ ?>
			</p>
		</div>
<?php   }   ?>
<?php
if($args['actions']['search']->wasCalled()) {
	eval("?>".file_get_contents("views/search.view.php"));
}
?>

</body>
</html>
