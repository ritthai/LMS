<?php
/**
	Given:	pagetitle,
			pageurl,
			actions
*/
?>

<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php	jsSetupAutocomplete('country_form', 'country', 'countries'); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>


<?php	$args['actions']['areas']->FORM_BEGIN('id="country_form"'); ?>
			<input type="text" name="country" id="country" <?php setDefaultText('Enter a country.'); ?> />
<?php	$args['actions']['areas']->FORM_END(); ?>

<!--
<?php	foreach($args['countries'] as $country) { ?>
			<a class="bodylink" href="/areas?country=<?php echo $country['id']; ?>">
				<?php echo $country['name']; ?>
			</a>
			<br/>
<?php	} ?>
-->

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

