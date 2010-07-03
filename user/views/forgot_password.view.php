<?php
/**
	Given:	pagetitle,
			actions
*/
?>

<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

<?php $args['actions']['forgot_password']->FORM_BEGIN(); ?>
	Username:		<input type="text" name="name" /><br/>
	Email address:	<input type="text" name="email" /><br/>
					<input type="submit" value="Send password reset instructions" />
<?php $args['actions']['forgot_password']->FORM_END(); ?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

