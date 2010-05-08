<ul>
<?php	foreach($_SESSION['google'] as $lst) {
			$title = $lst[0]; $link = $lst[1];
?>
	<li><a href="<?php echo $link; ?>"><?php echo $title; ?></a></li>
<?php	}	?>
</ul>
