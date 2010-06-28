<?php foreach($YT_RESULTS as $url) { ?>
	<object width="480" height="385">
		<param name="movie" value="<?php echo $url; ?>"></param>
		<param name="allowFullScreen" value="true"></param>
		<param name="allowscriptaccess" value="always"></param>
		<embed src="<?php echo $url; ?>" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="480" height="385"></embed>
		</object>
<?php	}	?>
