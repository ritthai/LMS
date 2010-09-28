<?php
/**
	Given:	pagetitle,
			actions
*/

?>
<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php   jsSetupAutocomplete(''/* don't submit form */, 'tgt', 'users'); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

<?php $args['actions']['submitprivatemessage']->FORM_BEGIN(); ?>
	<p>Username:</p>
	<input type="text" name="user" size="32" id="tgt" value="<?php echo isset($_SESSION['saved']['user']) ? $_SESSION['saved']['user'] : ''; ?>" />
	<p>Subject:</p>
	<input type="text" name="subject" size="64" value="<?php echo isset($_SESSION['saved']['subject']) ? $_SESSION['saved']['subject'] : ''; ?>" />
	<p>Message:</p>
	<textarea name="msg" rows="10" cols="60"><?php echo isset($_SESSION['saved']['msg']) ? $_SESSION['saved']['msg'] : ''; ?></textarea>
	

	<br><input type="submit" value="Send message" />
<?php $args['actions']['submitprivatemessage']->FORM_END(); ?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

