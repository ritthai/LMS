<?php	$tooltipdata = array();
		foreach($args['searchresults'] as $ok => $subject) {
			$cid = $subject['comment_id'];
			$jsid = uniqid();
			$flags = 0;
			$comments = $subject['comments'];
			$text = "Comment on &quot;".clean($subject['subject'])."&quot;";
?>

<div class="search_result">
<?php	$text = clean($subject['subject']); include("$ROOT/course/views/toggleswitch.pview.php"); ?>
<?php	startToggleBlock($jsid); ?>
		<div class="novid_result_content" id="novid_result_content_<?php echo $jsid; ?>">
<?php	/***** YOUTUBE *****/ ?>
<?php	if($subject['youtube'] && count($subject['youtube'])) { ?>
			<div class="vid_panel_horiz" id="vid_panel_<?php echo $jsid; ?>">
<?php		$default_caption = 'Mouse over a video to see its title.'; ?>
<?php		foreach($subject['youtube'] as $k=>$res) {
				//$width = $res['media:thumbnail']['width']; $height = $res['media:thumbnail']['height'];
				$width = 118; $height = 88;
				$id = "youtube$ok"."_$k";
				$src = $res['thumbnail_url'];
				$content = $res['src'];
				if($CONFIG['debug']) $fulltitle = clean($res['title'])."<br>Rating: $res[rating]";
				else $fulltitle = clean($res['title']);
				$tooltipdata[] = array('id'=>$id, 'fulltitle'=>$fulltitle);
				$title = clean(limit($res['title'], ' on YouTube', 65));
				$caption = "$('youtube_caption_$jsid')";
?>
				<div class="youtube_frame">
				<div	class="youtube_thumb"
						onmouseover="<?php echo $caption; ?>.innerHTML = '<?php echo escape_js($title); ?>';"
						onmouseout="<?php echo $caption; ?>.innerHTML = '<?php echo $default_caption; ?>';" >
					<a class="bodylink youtube_link" href="<?php echo $content;//$link; ?>" onclick="<?php showYouTubeVid($content); ?>" id="<?php echo $id; ?>" title="<?php echo $fulltitle; ?>">
						<img	src="<?php echo $src; ?>"
								width="<?php echo $width; ?>"
								height="<?php echo $height; ?>"
								title="<?php echo $title; ?>"
						/>
					</a>
				</div>
				</div>
				<?php //echo $res['title'] ? $res['title'].' on YouTube' : '-'; ?>
<?php		} ?>
				<div style="clear:both"></div>
				<div class="youtube_caption" id="youtube_caption_<?php echo $jsid; ?>">
					<?php echo $default_caption; ?>
				</div>
			</div>
<?php	} ?>

			<ul class="result_list">
<?php	/***** WIKIPEDIA *****/ ?>
<?php	if($subject['wikipedia'] && count($subject['wikipedia'])) { ?>
<?php		foreach($subject['wikipedia'] as $k=>$res) {
				$title = clean(limit(ucfirst(strtolower($res['title'])),' from Wikipedia',56));
				$link = $res['link'];
				$id = "wikipedia$ok"."_$k";
				if($CONFIG['debug']) $fulltitle = clean($res['title'])."<br>Rating: $res[rating]";
				else $fulltitle = clean($res['title']);
				$tooltipdata[] = array('id'=>$id, 'fulltitle'=>$fulltitle);
?>
		<a class="bodylink wikipedia_link" href="<?php echo $link; ?>" id="<?php echo $id; ?>" title="<?php echo $res['title']; ?>" target="_blank">
			<li id="<?php echo "li_$id"; ?>">
				<?php echo $title; ?>
				<a class="bodylink" id="report_<?php echo $res['id']; ?>" href="javascript:report('<?php echo User::GetAuthenticatedID(); ?>','<?php echo $res['id']; ?>','');">
					<div class="report">&nbsp;</div>
				</a>
				<a class="bodylink" id="vote_down_<?php echo $res['id']; ?>" href="javascript:voteDown('<?php echo $args['course']['id']; ?>','<?php echo $res['id']; ?>','<?php echo User::GetAuthenticatedID(); ?>','result');">
					<div class="voteDown">&nbsp;</div>
				</a>
				<a class="bodylink" id="vote_up_<?php echo $res['id']; ?>" href="javascript:voteUp('<?php echo $args['course']['id']; ?>','<?php echo $res['id']; ?>','<?php echo User::GetAuthenticatedID(); ?>','result');">
					<div class="voteUp">&nbsp;</div>
				</a>
			</li>
		</a>
<?php		} ?>
<?php	} ?>

<?php	/***** GOOGLE *****/ ?>
<?php	if($subject['google'] && count($subject['google'])) { ?>
<?php		foreach($subject['google'] as $k=>$res) {
				$title = clean(limit(ucfirst(strtolower($res['title'])),' from Google Search',56));
				$link = $res['link'];
				$id = "google$ok"."_$k";
				if($CONFIG['debug']) $fulltitle = clean($res['title'])."<br>Rating: $res[rating]";
				else $fulltitle = clean($res['title']);
				$tooltipdata[] = array('id'=>$id, 'fulltitle'=>$fulltitle);
?>
		<a class="bodylink google_link" href="<?php echo $link; ?>" id="<?php echo $id; ?>" title="<?php echo $fulltitle; ?>" target="_blank">
			<li id="<?php echo "li_$id"; ?>">
				<?php echo $title; ?>
				<a class="bodylink" id="report_<?php echo $res['id']; ?>" href="javascript:report('<?php echo User::GetAuthenticatedID(); ?>','<?php echo $res['id']; ?>','');">
					<div class="report">&nbsp;</div>
				</a>
				<a class="bodylink" id="vote_down_<?php echo $res['id']; ?>" href="javascript:voteDown('<?php echo $args['course']['id']; ?>','<?php echo $res['id']; ?>','<?php echo User::GetAuthenticatedID(); ?>','result');">
					<div class="voteDown">&nbsp;</div>
				</a>
				<a class="bodylink" id="vote_up_<?php echo $res['id']; ?>" href="javascript:voteUp('<?php echo $args['course']['id']; ?>','<?php echo $res['id']; ?>','<?php echo User::GetAuthenticatedID(); ?>','result');">
					<div class="voteUp">&nbsp;</div>
				</a>
			</li>
		</a>
<?php		} ?>
<?php	} ?>

<?php	/***** ITUNESU *****/ ?>
<?php	if($subject['itunesu'] && count($subject['itunesu'])) { ?>
<?php		foreach($subject['itunesu'] as $k=>$res) {
				$title = clean(limit(ucfirst(strtolower($res['title'])), ' on iTunes U', 56));
				$id = "itunesu$ok"."_$k";
				if($CONFIG['debug']) $fulltitle = clean($res['title'])."<br>Rating: $res[rating]";
				else $fulltitle = clean($res['title']);
				$tooltipdata[] = array('id'=>$id, 'fulltitle'=>$fulltitle);
?>
		<a class="bodylink itunesu_link" href="<?php echo $res['url']; ?>" id="<?php echo $id; ?>" title="<?php echo $fulltitle; ?>" target="_blank">
			<li id="<?php echo "li_$id"; ?>">
				<?php echo $title; ?>
				<a class="bodylink" id="report_<?php echo $res['id']; ?>" href="javascript:report('<?php echo User::GetAuthenticatedID(); ?>','<?php echo $res['id']; ?>','');">
					<div class="report">&nbsp;</div>
				</a>
				<a class="bodylink" id="vote_down_<?php echo $res['id']; ?>" href="javascript:voteDown('<?php echo $args['course']['id']; ?>','<?php echo $res['id']; ?>','<?php echo User::GetAuthenticatedID(); ?>','result');">
					<div class="voteDown">&nbsp;</div>
				</a>
				<a class="bodylink" id="vote_up_<?php echo $res['id']; ?>" href="javascript:voteUp('<?php echo $args['course']['id']; ?>','<?php echo $res['id']; ?>','<?php echo User::GetAuthenticatedID(); ?>','result');">
					<div class="voteUp">&nbsp;</div>
				</a>
			</li>
		</a>
<?php		} ?>
<?php	} ?>

<?php	/***** KHANACAD *****/ ?>
<?php	if($subject['khanacad'] && count($subject['khanacad'])) { ?>
<?php		foreach($subject['khanacad'] as $k=>$res) {
				$title = clean(limit(ucfirst(strtolower($res['title'])), ' on Khan Academy', 56));
				$id = "khanacad$ok"."_$k";
				if($CONFIG['debug']) $fulltitle = clean($res['title'])."<br>Rating: $res[rating]";
				else $fulltitle = clean($res['title']);
				$tooltipdata[] = array('id'=>$id, 'fulltitle'=>$fulltitle);
?>
		<a class="bodylink khanacad_link" href="<?php echo $res['url']; ?>" id="<?php echo $id; ?>" title="<?php echo $fulltitle; ?>" target="_blank">
			<li id="<?php echo "li_$id"; ?>">
				<?php echo $title; ?>
				<a class="bodylink" id="report_<?php echo $res['id']; ?>" href="javascript:report('<?php echo User::GetAuthenticatedID(); ?>','<?php echo $res['id']; ?>','');">
					<div class="report">&nbsp;</div>
				</a>
				<a class="bodylink" id="vote_down_<?php echo $res['id']; ?>" href="javascript:voteDown('<?php echo $args['course']['id']; ?>','<?php echo $res['id']; ?>','<?php echo User::GetAuthenticatedID(); ?>','result');">
					<div class="voteDown">&nbsp;</div>
				</a>
				<a class="bodylink" id="vote_up_<?php echo $res['id']; ?>" href="javascript:voteUp('<?php echo $args['course']['id']; ?>','<?php echo $res['id']; ?>','<?php echo User::GetAuthenticatedID(); ?>','result');">
					<div class="voteUp">&nbsp;</div>
				</a>
			</li>
		</a>
<?php		} ?>
<?php	} ?>

			</ul>
			<div style="clear:both"> </div>
		</div>

<?php   /*****  COMMENTS    *****/  ?>
<?php	$comment_stack[] = array($cid, $jsid, $flags, $comments, $text);
		$cid = $subject['comment_id'];
		$jsid = uniqid();
		$flags = 1;
		$text = "Comment on &quot;".clean($subject['subject'])."&quot;";
		include("$ROOT/course/views/comment.pview.php");
		list($cid, $jsid, $flags, $comments, $text) = array_pop($comment_stack);
?>
<?php	endToggleBlock($jsid); ?>
</div>

<?php } // for each subject ?>

<script type="text/javascript">
	/*
	jQuery('.youtube_link').qtip();
	jQuery('.google_link').qtip();
	jQuery('.itunesu_link').qtip();
	jQuery('.khanacad_link').qtip();*/
</script>

