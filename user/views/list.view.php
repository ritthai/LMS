<?php
/**
	Given:	pagetitle,
			actions,
			userlist :	(id,
						name,
						creation_timestamp)
*/
?>
<html>
	<head>
		<title><?php echo $args['pagetitle']; ?></title>
	</head>
	<body>
<?php   if($errors=Error::get()) {  ?>
        <div style="border: 1px solid #F00; background-color: #fff5f5;">
            <ul>
            <?php foreach($errors as $error) {  ?>
                <li><?php echo Error::format_error($error); ?></li>
            <?php } ?>
            </ul>
        </div>
<?php   }   ?>

		<table>
			<tr>
				<th>id</th>
				<th>name</th>
				<th>creation_timestamp</th>
			</tr>
<?php	foreach($args['userlist'] as $user) {	?>
			<tr>
				<td><a href="<?php echo $args['actions']['show']->getLink(array('userid'=>$user['id'])); ?>">
						<?php echo $user['id']; ?>
					</a>
				</td>
				<td><?php echo $user['name']; ?></td>
				<td><?php echo $user['creation_timestamp']; ?></td>
			</tr>
<?php	}	?>

	</body>
</html>
