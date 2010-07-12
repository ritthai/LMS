<?php foreach($args['searchresults'] as $subject) { ?>

<div style="border: 1px solid #000">
<ul>Subject: <?php echo $subject['subject']; ?>

<?php	if($subject['google'] && count($subject['google'])) { ?>
	<li><ul>Google results:
<?php		foreach($subject['google'] as $res) {
				$title = $res[0]; $link = $res[1];
?>
		<li><a href="<?php echo $link; ?>"><?php echo $title; ?></a></li>
<?php		} ?>
	</ul></li>
<?php	} ?>

<?php	if($subject['youtube'] && count($subject['youtube'])) { ?>
	<li>YouTube results:
		<table>
<?php		foreach($subject['youtube'] as $res) {
				$width = 480/2; $height = 385/2;
?>
		<tr>
			<td><?php echo $res['title'] ? $res['title'] : '-'; ?></td>
			<td><?php echo $res['media:description'] ? $res['media:description'] : '-'; ?></td>
			<td><?php echo $res['media:keywords'] ? $res['media:keywords'] : '-'; ?></td>
			<td><?php echo $res['category'] ? $res['category'] : '-'; ?></td>
			<td>
				<object width="<?php echo $width; ?>" height="<?php echo $height; ?>">
				<param name="movie" value="<?php echo $res['src']; ?>"></param>
				<param name="allowFullScreen" value="true"></param>
				<param name="allowscriptaccess" value="always"></param>
				<embed	src="<?php echo $url; ?>"
					type="application/x-shockwave-flash"
					allowscriptaccess="always" allowfullscreen="true"
					width="<?php echo $width; ?>" height="<?php echo $height; ?>">
				</embed>
				</object>
			</td>
		</tr>
<?			} ?>
	</table></li>
<?php	} ?>

<?php	if($subject['itunesu'] && count($subject['itunesu'])) { ?>
	<li><ul>iTunesU results:
<?php		foreach($subject['itunesu'] as $res) {
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
<?php	} ?>

<?php	if($subject['khanacad'] && count($subject['khanacad'])) { ?>
	<li><ul>Khan Academy results:
<?php		foreach($subject['khanacad'] as $res) { ?>
		<li>
			<a href="<?php echo $res['url']; ?>">
				<?php echo $res['title']; ?>
			</a>
		</li>
<?			} ?>
	</ul></li>
<?php	} ?>

</ul><?php // subject list ?>

</div>
<?php } // for each subject ?>

