<?php
/**
	Given:	pagetitle,
			pageurl,
			course:	title,
					code,
					descr
			searchresults,
			actions
*/
?>

<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>
		
<?php $args['actions']['search']->FORM_BEGIN(); ?>
	Enter the course code: <input type="text" id="terms" name="terms" value="<?php echo $args['course']['code']; ?>" /><br/>
	Tags (comma-delimited): <textarea cols=30 rows=3 id="tags" name="tags" /><?php echo $args['course']['tags']; ?></textarea><br/>
<input type="submit" value="Find resources" />
<?php $args['actions']['search']->FORM_END(); ?>

<?php   if($args['actions']['search']->wasCalled()) {  ?>
	Description: <textarea cols=40 rows=5 id="descr" name="descr" /><?php echo $args['course']['descr']; ?></textarea><br/>
<?		}	?>

<?php
if($args['actions']['search']->wasCalled()) { // are there params?
	include('views/dataacquisition.view.php');
}
?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

