<?php
/**
	Given:	pagetitle,
			actions,
			userinfo : id
*/
?>

<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>
		
<?php	if($args['userinfo']) { ?>
		<table>
<?php		foreach($args['userinfo'] as $val) { ?>
			<tr>
				<td><?php echo $val[0]; ?></td>
				<td><?php echo $val[1] ? $val[1] : 'Could not retrieve field'; ?></td>
			</tr>
<?php		}	?>
		</table>
<?php	}	?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

