<?php
/**
	Given:	pagetitle,
			actions,
			list :	id,
					subject,
					creation_timestamp
*/
?>

		<table>
			<tr>
				<th>id</th>
				<th>creation_timestamp</th>
			</tr>
<?php	foreach($args['list'] as $res) {	?>
			<tr>
				<td><a href="<?php echo $args['actions']['show']->getLink(array('id'=>$res['id'])); ?>">
						<?php echo $res['id']; ?>
					</a>
				</td>
				<td><?php echo $res['subject']; ?></td>
				<td><?php echo $res['creation_timestamp']; ?></td>
			</tr>
<?php	}	?>


