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
<html>
	<head>
		<title><?php echo $args['pagetitle']; ?></title>
	</head>
	<body>
<?php	if($errors=Error::get()) {	?>
		<div style="border: 1px solid #F00; background-color: #fff5f5;">
			<ul>
			<?php foreach($errors as $error) {	?>
				<li><?php echo Error::format_error($error); ?></li>
			<?php }	?>
			</ul>
		</div>
<?php	}	?>
		<center><img src="<?php echo $HTMLROOT; ?>/images/logo.png" /></center>
		
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
	eval("?>".file_get_contents("views/dataacquisition.view.php"));
}
?>

</body>
</html>
