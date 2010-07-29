<?php
/**
	Given:	pagetitle,
			pageurl,
			actions
*/
?>

<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php	//jsSetupAutocomplete('area_form', 'area', 'areas'); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

<!--
<?php	$args['actions']['universities']->FORM_BEGIN('id="area_form"'); ?>
			<input type="text" name="area" id="area" <?php setDefaultText('Enter a province.'); ?> />
<?php	$args['actions']['universities']->FORM_END(); ?>
-->
<?php	foreach($args['areas'] as $area) { ?>
			<a class="bodylink" href="/universities?area=<?php echo $area['id']; ?>">
				<?php echo $area['name']; ?>
			</a>
			<br/>
<?php	} ?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

