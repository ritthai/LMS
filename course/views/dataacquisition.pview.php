<?php foreach($args['searchresults'] as $subject) { ?>

<div class="search_result">
	<a href="javascript:toggleContentPanel(<?php echo $subject['comment_id']; ?>);">
		<div class="expand" id="expand_<?php echo $subject['comment_id']; ?>">&nbsp;</div>
		<div class="topic_name"><?php echo clean($subject['subject']); ?></div>
	</a>
	<div class="all_result_content" id="all_result_content_<?php echo $subject['comment_id']; ?>">
<?php	if(false && !User::AuthenticatedUserHasMatchingAttrib('topicfav', $subject['comment_id'])) { ?>
		<div id="favs_<?php echo $subject['comment_id']; ?>">
			<a	class="bodylink" id="add_to_favs_<?php echo $subject['comment_id']; ?>"
				href="javascript:addToFavs(	'<?php echo $subject['comment_id']; ?>',
											'<?php echo User::GetAuthenticatedID(); ?>',
											'topic',
											'<?php echo clean($subject['subject']); ?>' );">
				<div class="addToFavs">&nbsp;</div>
				[Add to favs]
			</a>
		</div>
<?php	} ?>
<?php	if(false && $subject['youtube'] && count($subject['youtube'])) { ?>
		<div>
			<a href="javascript:toggleVidPanel(<?php echo $subject['comment_id']; ?>);">
				<div class="addToFavs">&nbsp;</div>
				[Toggle vids]
			</a>
		</div>
		<div class="result_content" id="result_content_<?php echo $subject['comment_id']; ?>">
<?php	} else { ?>
		<div class="novid_result_content" id="novid_result_content_<?php echo $subject['comment_id']; ?>">
<?php	} ?>
<?php	if($subject['youtube'] && count($subject['youtube'])) { ?>
			<div class="vid_panel_horiz" id="vid_panel_<?php echo $subject['comment_id']; ?>">
<?php		$default_caption = 'Mouse over a video to see its title.'; ?>
<?php		foreach($subject['youtube'] as $k=>$res) {
				//$width = $res['media:thumbnail']['width']; $height = $res['media:thumbnail']['height'];
				$width = 118; $height = 88;
				$src = $res['media:thumbnail']['url']; $link = $res['link'];
				$content = $res['src'];
				$title = clean(limit($res['title'], ' on YouTube', 65));
				$caption = "$('youtube_caption_$subject[comment_id]')";
?>
				<div class="youtube_frame">
				<div	class="youtube_thumb"
						onmouseover="<?php echo $caption; ?>.innerHTML = '<?php echo $title; ?>';"
						onmouseout="<?php echo $caption; ?>.innerHTML = '<?php echo $default_caption; ?>';" >
					<a class="bodylink youtube_link" href="<?php echo $link; ?>" onclick="<?php showYouTubeVid($content); ?>">
						<img	src="<?php echo $src; ?>"
								width="<?php echo $width; ?>"
								height="<?php echo $height; ?>"
								title="<?php echo $title; ?>"
						/>
					</a>
				</div>
				</div>
				<?php //echo $res['title'] ? $res['title'].' on YouTube' : '-'; ?>
<?			} ?>
				<div style="clear:both"></div>
				<div class="youtube_caption" id="youtube_caption_<?php echo $subject['comment_id']; ?>">
					<?php echo $default_caption; ?>
				</div>
			</div>
<?php	} ?>

			<ul class="result_list">
<?php	if($subject['google'] && count($subject['google'])) { ?>
<?php		foreach($subject['google'] as $res) {
				$title = clean(limit($res[0],' from Google Search',65));
				$link = $res[1];
?>
				<li>
					<a class="bodylink" href="<?php echo $link; ?>">
						<?php echo $title; ?>
					</a>
				</li>
<?php		} ?>
<?php	} ?>

<?php	if($subject['itunesu'] && count($subject['itunesu'])) { ?>
<?php		foreach($subject['itunesu'] as $res) {
				$title = clean(limit($res['title'], ' on iTunes U', 65));
?>
		<li>
			<a class="bodylink" href="<?php echo $res['url']; ?>">
				<?php echo $title; ?>
			</a>
		</li>
<?php		} ?>
<?php	} ?>

<?php	if($subject['khanacad'] && count($subject['khanacad'])) { ?>
<?php		foreach($subject['khanacad'] as $res) {
				$title = clean(limit($res['title'], ' on Khan Academy', 65));
?>
		<li>
			<a class="bodylink" href="<?php echo $res['url']; ?>">
				<?php echo $title; ?>
			</a>
		</li>
<?			} ?>
<?php	} ?>

			</ul>
			<div style="clear:both"> </div>
		</div>

<?php   $boxid = $subject['comment_id'] + 1000000000; ?>
<div class="search_result">
    <a href="javascript:toggleContentPanel(<?php echo $boxid; ?>);">
        <div class="expand" id="expand_<?php echo $boxid; ?>">&nbsp;</div>
        <div class="topic_name">Comment on &quot;<?php echo clean($subject['subject']); ?>&quot;</div>
    </a>
    <div class="all_result_content" id="all_result_content_<?php echo $boxid; ?>">
<?php   /*****  COMMENTS    *****/  ?>
<?php   $cid = $subject['comment_id']; $comments = $subject['comments'];
		include("$ROOT/course/views/comment.pview.php");
?>
	</div>
</div>

	</div>
</div>

<?php } // for each subject ?>

