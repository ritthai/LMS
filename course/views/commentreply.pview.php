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
                                        'comment_box_<?php echo $jsid; ?>')" />
<?php $args['actions']['post']->AJAX_FORM_END(); ?>
</div>
