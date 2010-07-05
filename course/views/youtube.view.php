<?php foreach($YT_RESULTS as $res) { ?>
	<object width="480" height="385">
		<param name="movie" value="<?php echo $res['src']; ?>"></param>
		<param name="allowFullScreen" value="true"></param>
		<param name="allowscriptaccess" value="always"></param>
		<embed src="<?php echo $url; ?>" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="480" height="385"></embed>
		</object>
<?php	}	?>
