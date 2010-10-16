<?php /* $cid,$jsid,$flags,$comments,$text */ ?>
<?php	if($cid > 1) { ?>
<div class="search_result">
<?php	include("$ROOT/course/views/toggleswitch.pview.php"); ?>
<?php	startToggleBlock($jsid); ?>

<div id="comment_box_<?php echo $jsid; ?>">
<?php   if($comments) { ?>
<?php		foreach($comments as $id) {
				if(is_array($id)) { // array of objects, not ids :-/
					$id = $id['id'];
				}
				$subject = Comment::GetSubject($id);
				$timestamp = Comment::GetTimestamp($id);
				$ownerid = Comment::GetAttrib($id, 'owner');
				$owner = User::GetAttrib($ownerid, 'name');
				$body = Comment::GetAttrib($id, 'body');
				$rating = Comment::GetAttrib($id, 'rating');
				$comment_stack[] = array($cid, $jsid, $flags, $comments, $text);
				$cid = $id;
				$jsid = uniqid();
				$flags = 1;
				$comments = Comment::ListAll($cid, 1);
				$text = "See more comments ";
?>
	<div class="comment">
		<div class="avatar">
			<div class="user_image">
				<img src="<?php echo User::GetAvatar($ownerid); ?>" />
			</div>
		</div>
        <h5><?php echo $subject; ?>
			- Posted at <?php echo $timestamp; ?>
			by <?php echo $owner; ?>
			<a class="bodylink" id="report_<?php echo $cid; ?>" href="javascript:report('<?php echo User::GetAuthenticatedID(); ?>','<?php echo $cid; ?>','');">
				<div class="report">&nbsp;</div>
			</a>
			<a class="bodylink" id="vote_down_<?php echo $cid; ?>" href="javascript:voteDown('<?php echo $args['course']['id']; ?>','<?php echo $cid; ?>','<?php echo User::GetAuthenticatedID(); ?>','result');">
				<div class="voteDown">&nbsp;</div>
			</a>
			<a class="bodylink" id="vote_up_<?php echo $cid; ?>" href="javascript:voteUp('<?php echo $args['course']['id']; ?>','<?php echo $cid; ?>','<?php echo User::GetAuthenticatedID(); ?>','result');">
				<div class="voteUp">&nbsp;</div>
			</a>
			- <?php echo $rating; ?> Points
		</h5>
        <p><?php echo $body; ?></p>
<?php	$comment_stack[] = array($cid, $jsid, $flags, $comments, $text);
		$jsid = uniqid();
		$text = "Reply";
		include("$ROOT/course/views/toggleswitch.pview.php");
		startToggleBlock($jsid);
		include("$ROOT/course/views/commentreply.pview.php");
		endToggleBlock($jsid);
		list($cid, $jsid, $flags, $comments, $text) = array_pop($comment_stack);
?>
    </div>

<?php			if($comments && count($comments) > 0) include("$ROOT/course/views/comment.pview.php");
				list($cid, $jsid, $flags, $comments, $text) = array_pop($comment_stack);
			}
		}
?>
</div>

<?php		include("$ROOT/course/views/commentreply.pview.php"); ?>

	</div>
<?php		endToggleBlock($jsid); ?>
<?php	} // if($cid != 0) ?>
