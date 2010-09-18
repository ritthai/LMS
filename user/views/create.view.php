<?php
/**
	Given:	pagetitle,
			actions,
			recaptcha_error
*/

?>
<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php   jsSetupAutocomplete(''/* don't submit form */, 'uni', 'universities'); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

<?php $args['actions']['create']->FORM_BEGIN(); ?>
	<p>Username:</p>
	<input type="text" name="name" value="<?php echo $args['name']; ?>" /><br/>
	<p>Real name:</p>
	<input type="text" name="realname" value="<?php echo $args['realname']; ?>" /><br/>
	<p>University:</p>
	<input type="text" name="university" id="uni" style="color: #000" value="<?php echo $args['university']; ?>" /><br/>
	<p>Year of Graduation:</p>
	<input type="text" name="gradyear" value="<?php echo $args['gradyear']; ?>" /><br/>
	<p>Email address:</p>
	<input type="text" name="email" value="<?php echo $args['email']; ?>" /><br/>
	<p>Password:</p>
	<input type="password" name="password" value="<?php echo $args['password']; ?>" /><br/>
<?php	if(User::HasPermissions('admin')) { ?>
	<p>Role:</p>
	<input type="text" name="role" value="banned,admin" /><br/>
<?php	} ?>
					<?php echo recaptcha_get_html($CONFIG['recaptcha_pubkey'], $args['recaptcha_error']); ?>
					<input type="submit" value="Create account" />
<?php $args['actions']['create']->FORM_END(); ?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

