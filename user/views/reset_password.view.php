<?php
/**
	Given:	pagetitle,
			actions,
			id,
			key
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

<?php $args['actions']['reset_password']->FORM_BEGIN(); ?>
	Password:		<input type="password" name="password" /><br/>
					<input type="hidden" name="id" value="<?php echo $args['id']; ?>" />
					<input type="hidden" name="key" value="<?php echo $args['key']; ?>" />
					<input type="submit" value="Reset password" />
<?php $args['actions']['reset_password']->FORM_END(); ?>
	</body>
</html>
