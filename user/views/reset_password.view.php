<?php
/**
	Given:	pagetitle,
			actions,
			id,
			key
*/
?>

<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

<?php $args['actions']['reset_password']->FORM_BEGIN(); ?>
	Password:		<input type="password" name="password" /><br/>
					<input type="hidden" name="id" value="<?php echo $args['id']; ?>" />
					<input type="hidden" name="key" value="<?php echo $args['key']; ?>" />
					<input type="submit" value="Reset password" />
<?php $args['actions']['reset_password']->FORM_END(); ?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

