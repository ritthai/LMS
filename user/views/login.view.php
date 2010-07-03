<?php
/**
	Given:	pagetitle,
			actions
*/
?>
<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

<?php $args['actions']['login']->FORM_BEGIN(); ?>
	Username:		<input type="text" name="name" /><br/>
	Password:		<input type="password" name="password" /><br/>
					<input type="submit" value="Log in" />
<?php $args['actions']['login']->FORM_END(); ?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

