<?php
/**
	Given:	pagetitle,
			actions,
			privatemessages
*/
?>

<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

	<p>Message from:</p>
	<input type="text" name="creator" size="32" value="<?php echo $args['privatemessage']['creator']; ?>" />
	<p>Date sent:</p>
	<input type="text" name="creation_timestamp" size="32" value="<?php echo $args['privatemessage']['creation_timestamp']; ?>" />
	<p>Subject:</p>
	<input type="text" name="subject" size="32" value="<?php echo $args['privatemessage']['subject']; ?>" />
	<p>Message:</p>
	<textarea name="msg" rows="10" cols="60"><?php echo $args['privatemessage']['message']; ?></textarea>
	
<?php include("$TEMPLATEROOT/template_end.inc"); ?>

