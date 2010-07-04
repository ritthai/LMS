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

<h2><?php echo $args['course']['code']; ?>: <?php echo $args['course']['title']; ?></h2>
<p><?php echo $args['course']['descr']; ?></p>

<?php
if($args['searchresults'])
	include('views/dataacquisition.view.php');
?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

