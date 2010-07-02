<?php
/**
	Given:	pagetitle,
			pageurl,
			courses,
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

		<table>
			<tr>
				<th>Title</th>
				<th>Code</th>
				<th>Description</th>
			</tr>
<?php	foreach($args['courses'] as $course) {	?>
			<tr>
				<td><?php echo $course['title']; ?></td>
				<td><?php echo $course['code']; ?></td>
				<td><?php echo $course['descr']; ?></td>
			</tr>
<?php	}	?>
		</table>

</body>
</html>
