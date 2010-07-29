<?php
/**
	Given:	pagetitle,
			pageurl,
			actions
*/
?>

<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php	//jsSetupAutocomplete('uni_form', 'uni', 'universities'); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

<!--
<?php	$args['actions']['subjects']->FORM_BEGIN('id="uni_form"'); ?>
			<input type="text" name="uni" id="uni" <?php setDefaultText('Enter a university'); ?> />
<?php	$args['actions']['subjects']->FORM_END(); ?>
-->

<?php   foreach($args['universities'] as $uni) { ?>
            <a class="bodylink" href="/subjects?university=<?php echo $uni['id']; ?>">
                <?php echo $uni['name']; ?>
            </a>
            <br/>
<?php   } ?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

