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

<?php $args['actions']['login']->FORM_BEGIN(); ?>
	Username:		<input type="text" name="name" /><br/>
	Password:		<input type="password" name="password" /><br/>
					<input type="submit" value="Log in" />
<?php $args['actions']['login']->FORM_END(); ?>
	</body>
</html>
