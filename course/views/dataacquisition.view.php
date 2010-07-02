<?php foreach($args['searchresults'] as $subject) { ?>
<div style="border: 1px solid #000">
<ul>Subject: <?php echo $subject['subject']; ?>
	<li><ul>Google results:
<?php	foreach($subject['google'] as $res) {
			$title = $res[0]; $link = $res[1];
?>
		<li><a href="<?php echo $link; ?>"><?php echo $title; ?></a></li>
<?		} ?>
	</ul></li>
	<li><ul>YouTube results:
<?php	foreach($subject['youtube'] as $res) {
			$width = 480/2; $height = 385/2;
?>
		<li>
			<object width="<?php echo $width; ?>" height="<?php echo $height; ?>">
			<param name="movie" value="<?php echo $res; ?>"></param>
			<param name="allowFullScreen" value="true"></param>
			<param name="allowscriptaccess" value="always"></param>
			<embed	src="<?php echo $url; ?>"
					type="application/x-shockwave-flash"
					allowscriptaccess="always" allowfullscreen="true"
					width="<?php echo $width; ?>" height="<?php echo $height; ?>">
			</embed>
			</object>
		</li>
<?		} ?>
	</ul></li>
	<li><ul>iTunesU results:
<?php	foreach($subject['itunesu'] as $res) {
			$imgwidth = $imgheight = 160;
?>
		<li>
			<img	src="<?php echo $res['art']; ?>" style="float: left"
					width=<?php echo $imgwidth; ?> height=<?php echo $imgheight; ?> />
			<a href="<?php echo $res['url']; ?>" style="float: right; margin-right: 400px; margin-top: 80px">
				<?php echo $res['title']; ?>
			</a>
			<div style="clear:both"></div>
		</li>
<?		} ?>
	</ul></li>
	<li><ul>Khan Academy results:
<?php	foreach($subject['khanacad'] as $res) { ?>
		<li>
			<a href="<?php echo $res['url']; ?>">
				<?php echo $res['title']; ?>
			</a>
		</li>
<?		} ?>
	</ul></li>
</ul>
</div>
<?php } ?>
