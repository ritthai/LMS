<?php
/**
	Given:	pagetitle,
			actions,
			filelist :	(id,
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
<?php	foreach($args['filelist'] as $file) {	?>
			<tr>
				<td><a href="<?php echo $args['actions']['show']->getLink(array('fileid'=>$file['id'])); ?>">
						<?php echo $file['id']; ?>
					</a>
				</td>
				<td><?php echo $file['name']; ?></td>
				<td><?php echo $file['creation_timestamp']; ?></td>
			</tr>
<?php	}	?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

