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
				<th>subject</th>
				<th>from</th>
				<th>creation_timestamp</th>
			</tr>
<?php	foreach($args['privatemessages'] as $pm) {	?>
<?php		$cls = $pm['flags'] & 1 ? 'read_pm' : 'unread_pm'; ?>
			<tr>
				<td><a href="<?php echo $args['actions']['showprivatemessage']->getLink(array('id'=>$pm['id'])); ?>">
						<div class="<?php echo $cls; ?>"><?php echo $pm['subject']; ?></div>
					</a>
				</td>
				<td><a href="<?php echo $args['actions']['showprivatemessage']->getLink(array('id'=>$pm['id'])); ?>">
						<div class="<?php echo $cls; ?>"><?php echo $pm['creator']; ?></div>
					</a>
				</td>
				<td><a href="<?php echo $args['actions']['showprivatemessage']->getLink(array('id'=>$pm['id'])); ?>">
						<div class="<?php echo $cls; ?>"><?php echo $pm['creation_timestamp']; ?></div>
					</a>
				</td>
			</tr>
<?php	}	?>
		</table>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

