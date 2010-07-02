<?php
/**
	Given:	pagetitle,
			pageurl,
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

		<a href="list">list</a> or <a href="search">search</a>
</body>
</html>
