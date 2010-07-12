<?php
/**
	Given:	pagetitle,
			actions,
*/
?>

<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

<?php	$args['actions']['create2']->FORM_BEGIN();	?>
		Subject:		<input type="text" name="subject" /><br/>
		Parent ID:		<input type="text" name="id" /><br/>
		Body:			<textarea name="body" rows="3" cols="40"></textarea><br/>
						<input type="submit" value="Post" />
<?php	$args['actions']['create2']->FORM_END();	?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

