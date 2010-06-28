<?php	foreach($G_RESULTS as $subject) {
			foreach($subject as $result) {
				$title = $result[0]; $link = $result[1];
				$sub = $subject[1];
?>
<dl>
<lh><?php echo $sub; ?></lh>
	<dd><a href="<?php echo $link; ?>"><?php echo $title; ?></a></dd>
</dl>
<?php		}
		}
?>
