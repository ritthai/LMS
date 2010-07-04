<?php
/**
	Given:	pagetitle,
			actions,
			recaptcha_error
*/

?>
<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

<?php $args['actions']['create']->FORM_BEGIN(); ?>
	Username:		<input type="text" name="name" /><br/>
	Email address:	<input type="text" name="email" /><br/>
	Password:		<input type="password" name="password" /><br/>
<?php	if(User::HasPermissions('admin')) { ?>
	Role:			<input type="text" name="role" value="banned,admin" /><br/>
<?php	} ?>
					<?php echo recaptcha_get_html($CONFIG['recaptcha_pubkey'], $args['recaptcha_error']); ?>
					<input type="submit" value="Create account" />
<?php $args['actions']['create']->FORM_END(); ?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

