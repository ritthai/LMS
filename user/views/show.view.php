<?php
/**
	Given:	pagetitle,
			actions,
			userinfo : id
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
<?php	foreach($args['userinfo'] as $key=>$val) {	?>
			<tr>
				<td><?php echo $key; ?></td>
				<td><?php echo $val ? $val : 'Could not retrieve field'; ?></td>
			</tr>
<?php	}	?>
		</table>
	</body>
</html>
