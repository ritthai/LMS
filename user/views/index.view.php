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
		<ul>
			<li><a href="create">create</a></li>
			<li><a href="list">list</a></li>
			<li><a href="login">login</a></li>
			<li><a href="logout">logout</a></li>
			<li><a href="status">status</a></li>
		</ul>
	</body>
</html>
