<div id="comment_box_<?php echo $cid; ?>">
<?php   if($comments) { ?>
<?php		foreach($comments as $id) {
	$subject = Comment::GetSubject($id);
	$timestamp = Comment::GetTimestamp($id);
	$ownerid = Comment::GetAttrib($id, 'owner');
	$owner = User::GetAttrib($ownerid, 'name');
    $body = Comment::GetAttrib($id, 'body');
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
		</h5>
        <p><?php echo $body; ?></p>
    </div>

	<a href="javascript:toggleContentPanel(<?php echo $id; ?>);">
		Reply to comment
	</a>
	<div class="comment_box" id="comment_box_<?php echo $id; ?>" style="display:none">
		<?php $args['actions']['post']->AJAX_FORM_BEGIN('class="comment_form"'); ?>
Leave a comment:<br/>
Subject:	<input style="margin-left: 15px; margin-bottom: 5px" size=30 type="text" name="subject" value="" /><br>
Comment:	<textarea cols=40 rows=5 name="body"> </textarea><br>
			<input type="hidden" name="course" value="<?php echo $args['course']['code']; ?>" />
			<input type="hidden" name="owner" value="<?php echo User::GetAuthenticatedAttrib('name'); ?>" />
			<input type="hidden" name="cid" value="<?php echo $id; ?>" />
			<input class="submit_comment" type="image" src="/images/navigation/comment.png" name="submit" onclick="javascript:submit_comment(
				this.parentNode,
				'comment_box_<?php echo $id; ?>')" />
		<?php $args['actions']['post']->AJAX_FORM_END(); ?>
	</div>
<?php       }   ?>
<?php   }   ?>
</div>

<div class="comment_box">
<?php $args['actions']['post']->AJAX_FORM_BEGIN('class="comment_form"'); ?>
    Leave a comment:<br/>
    Subject:        <input style="margin-left: 15px; margin-bottom: 5px" size=30 type="text" name="subject" value="" /><br>
    Comment:        <textarea cols=40 rows=5 name="body"></textarea><br>
                    <input type="hidden" name="course" value="<?php echo $args['course']['code']; ?>" />
                    <input type="hidden" name="owner" value="<?php echo User::GetAuthenticatedAttrib('name'); ?>" />
                    <input type="hidden" name="cid" value="<?php echo $cid; ?>" />
					<input class="submit_comment" type="image" src="/images/navigation/comment.png" name="submit"
                            onclick="javascript:submit_comment(
                                        this.parentNode,
                                        'comment_box_<?php echo $cid; ?>')" />
<?php $args['actions']['post']->AJAX_FORM_END(); ?>
</div>
