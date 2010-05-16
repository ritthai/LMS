<ul>
<?php	foreach($iTU_RESULTS as $lst) {	?>
	<li><img src="<?php echo $lst['art']; ?>" />
		<a href="<?php echo $lst['url']; ?>"><?php echo $lst['title']; ?></a></li>
<?php	}	?>
</ul>
