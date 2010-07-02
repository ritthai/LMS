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

<?php $args['actions']['create']->FORM_BEGIN(); ?>
	Username:		<input type="text" name="name" /><br/>
	Email address:	<input type="text" name="email" /><br/>
	Password:		<input type="password" name="password" /><br/>
<?php	if(User::HasPermissions('admin')) { ?>
	Role:			<input type="text" name="role" value="banned,admin" /><br/>
<?php	} ?>
					<input type="submit" value="Create account" />
<?php $args['actions']['create']->FORM_END(); ?>
	</body>
</html>
