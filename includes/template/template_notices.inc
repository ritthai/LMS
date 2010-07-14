<?php
/**
	Given:	N/A
*/
?>
</head>
    <body>
<?php	if(User::IsAuthenticated() && ($id = User::GetAuthenticatedID())) {
			echo 'Logged in as '.User::GetAttrib($id, 'name');
			echo ' | <a href="/user/logout">Log out</a>';
		} else {
			echo '<a href="/user/login">Log in</a>';
		}
?>

<hr/>

<?php   if($errors=Error::get()) {  ?>
        <div style="border: 1px solid #F00; background-color: #fff5f5;">
            <ul>
            <?php foreach($errors as $error) {  ?>
                <li><?php echo Error::format_error($error); ?></li>
            <?php } ?>
            </ul>
        </div>
<?php   }   ?>

<!--<center><img src="<?php echo $HTMLROOT; ?>/images/logo.png" /></center>-->

