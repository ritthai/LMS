<?php foreach($args['searchresults'] as $subject) { ?>

<div style="border: 1px solid #000">

<p>Subject: <?php echo $subject['subject']; ?></p>
<hr/>

<table>
<tr>
<td style='width:160px'>
<?php	if($subject['youtube'] && count($subject['youtube'])) { ?>
<?php		foreach($subject['youtube'] as $res) {
				//$width = $res['media:thumbnail']['width']; $height = $res['media:thumbnail']['height'];
				$width = 240; $height = 180;
				$src = $res['media:thumbnail']['url']; $link = $res['link'];
?>
				<a href="<?php echo $link; ?>">
					<img	src="<?php echo $src; ?>"
							width="<?php echo $width; ?>"
							height="<?php echo $height; ?>"
					/>
				</a><br/>
				<?php echo $res['title'] ? $res['title'].' on YouTube' : '-'; ?>
				<hr/>
<?			} ?>
<?php	} ?>
</td>

<td>
<ul>
<?php	if($subject['google'] && count($subject['google'])) { ?>
<?php		foreach($subject['google'] as $res) {
				$title = $res[0]; $link = $res[1];
?>
		<li><a href="<?php echo $link; ?>"><?php echo $title.' from Google Search'; ?></a></li>
<?php		} ?>
<?php	} ?>

<?php	if($subject['itunesu'] && count($subject['itunesu'])) { ?>
<?php		foreach($subject['itunesu'] as $res) { ?>
		<li>
			<a href="<?php echo $res['url']; ?>">
				<?php echo $res['title'].' on iTunes U'; ?>
			</a>
		</li>
<?php		} ?>
<?php	} ?>

<?php	if($subject['khanacad'] && count($subject['khanacad'])) { ?>
<?php		foreach($subject['khanacad'] as $res) { ?>
		<li>
			<a href="<?php echo $res['url']; ?>">
				<?php echo $res['title'].' on Khan Academy'; ?>
			</a>
		</li>
<?			} ?>
<?php	} ?>

</ul>
</td>
</tr>
</table>

</div>
<?php } // for each subject ?>

