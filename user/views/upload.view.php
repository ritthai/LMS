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
		
<?php	$args['actions']['upload']->FORM_BEGIN('enctype="multipart/form-data"');	?>
									<input type="hidden" name="MAX_FILE_SIZE" value="4194304" /> <!-- 4 MB -->
		<!--File to upload:-->		<input type="file" name="file" /><br/>
									<input type="submit" value="Upload" />
<?php	$args['actions']['upload']->FORM_END();	?>
	</body>
</html>
