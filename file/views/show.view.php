<?php
/**
	Given:	pagetitle,
			actions,
			fileinfo : id
*/
?>

<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

<?php	if($args['fileinfo']) { ?>
		<table>
<?php		foreach($args['fileinfo'] as $val) {	?>
			<tr>
				<td><?php echo $val[0]; ?></td>
				<td><?php echo $val[1] ? $val[1] : 'Could not retrieve field'; ?></td>
			</tr>
<?php		}	?>
		</table>
<?php	}	?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

