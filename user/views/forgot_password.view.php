<?php
/**
	Given:	pagetitle,
			actions
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

<?php $args['actions']['forgot_password']->FORM_BEGIN(); ?>
	Username:		<input type="text" name="name" /><br/>
	Email address:	<input type="text" name="email" /><br/>
					<input type="submit" value="Send password reset instructions" />
<?php $args['actions']['forgot_password']->FORM_END(); ?>
	</body>
</html>
