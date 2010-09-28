<?php
/**
	Given:	pagetitle,
			actions,
			privatemessages
*/
?>

<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

		<p>
			<a href="<?php echo $args['actions']['privatemessage']->getLink(); ?>">
				Compose a new private message.
			</a>
		</p>

		<table>
			<tr>
				<th>id</th>
				<th>subject</th>
				<th>creation_timestamp</th>
			</tr>
<?php	foreach($args['privatemessages'] as $pm) {	?>
			<tr>
				<td><a href="<?php echo $args['actions']['showprivatemessage']->getLink(array('id'=>$pm['id'])); ?>">
						<?php echo $pm['id']; ?>
					</a>
				</td>
				<td><a href="<?php echo $args['actions']['showprivatemessage']->getLink(array('id'=>$pm['id'])); ?>">
						<?php echo $pm['subject']; ?>
					</a>
				</td>
				<td><a href="<?php echo $args['actions']['showprivatemessage']->getLink(array('id'=>$pm['id'])); ?>">
						<?php echo $pm['creation_timestamp']; ?>
					</a>
				</td>
			</tr>
<?php	}	?>
		</table>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

