<?php
/**
	Given:	pagetitle,
			pageurl,
			subject  :	title,
						code,
						descr
			courses  :	id,
						title,
						code,
						descr
			actions
*/
?>

<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

<?php	$title	= $args['subject']['title'];
		$code	= $args['subject']['code'];
		$descr	= $args['subject']['descr'];
?>
<h3><?php echo "$title ($code)"; ?></h3>
<!--<p> <?php echo "$descr"; ?> </p>

<hr/>-->

<?php	usort($args['courses'], function($a,$b) { return $a['code'] > $b['code']; });
		foreach($args['courses'] as $course) { ?>
	<p>
		<a class="bodylink" href="/search?id=<?php echo $course['id']; ?>">
			<?php echo "($course[code]) $course[title]"; ?>
		</a>
	</p>
<?php	} ?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

