<?php
/**
	Given:	pagetitle,
			actions,
			userlist :	(id,
						name,
						creation_timestamp)
*/
?>

<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

		<table>
			<tr>
				<th>id</th>
				<th>name</th>
				<th>creation_timestamp</th>
			</tr>
<?php	foreach($args['userlist'] as $user) {	?>
			<tr>
				<td><a href="<?php echo $args['actions']['show']->getLink(array('userid'=>$user['id'])); ?>">
						<?php echo $user['id']; ?>
					</a>
				</td>
				<td><?php echo $user['name']; ?></td>
				<td><?php echo $user['creation_timestamp']; ?></td>
			</tr>
<?php	}	?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

