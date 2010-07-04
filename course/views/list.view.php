<?php
/**
	Given:	pagetitle,
			pageurl,
			courses,
			actions
*/
?>

<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

		<table>
			<tr>
				<th>Title</th>
				<th>Code</th>
				<th>Description</th>
			</tr>
<?php	foreach($args['courses'] as $course) {	?>
			<tr>
				<td>	<a href="<?php echo $ACTIONS['show']->getLink(array('id'=>$course['id'])); ?>">
							<?php echo $course['title']; ?>
						</a>
				</td>
				<td><?php echo $course['code']; ?></td>
				<td><?php echo $course['descr']; ?></td>
			</tr>
<?php	}	?>
		</table>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

