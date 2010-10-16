<?php
/**
	Given:	pagetitle,
			pageurl,
			course:	title,
					code,
					descr
			comments: id,
			searchresults,
			actions
*/
?>

<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

<!--<?php	/*****	FAVOURITES	*****/	?>
<div id="fav_block">
<?php	include("$ROOT/course/views/favs.pview.php"); ?>
</div>

<?php	/*****	ADD COURSE TO FAVOURITES	*****/	?>
<?php   if(!User::AuthenticatedUserHasMatchingAttrib('coursefav', $args['comment_id'])) { ?>
        <div id="favs_<?php echo $args['comment_id']; ?>">
            <a  class="bodylink add_to_favs" id="add_to_favs_<?php echo $args['comment_id']; ?>"
				href="javascript:addToFavs(	'<?php echo $args['comment_id']; ?>',
											'<?php echo User::GetAuthenticatedID(); ?>',
											'course',
											'<?php echo $args['course']['title']; ?>' );">
                <div class="addToFavs">&nbsp;</div>
                [Add course to favs]
            </a>
        </div>
<?php   } ?>-->

<?php	/*****	COURSE DESCRIPTION	*****/	?>
<?php   if($action == 'search') {  ?>
	<div id="description_word">Description:</div>
	<p id="description">
		<?php echo clean($args['course']['descr']); ?>
	</p>
	<p>
		<a class="bodylink" id="report_<?php echo $args['comment_id']; ?>" href="javascript:report('<?php echo User::GetAuthenticatedID(); ?>','<?php echo $args['comment_id']; ?>','',0);">
			<div class="report">&nbsp;</div>
		</a>
		<a class="bodylink" id="vote_down_<?php echo $args['comment_id']; ?>" href="javascript:voteDown('<?php echo $args['course']['id']; ?>','<?php echo $args['comment_id']; ?>','<?php echo User::GetAuthenticatedID(); ?>','result');">
			<div class="voteDown">&nbsp;</div>
		</a>
		<a class="bodylink" id="vote_up_<?php echo $args['comment_id']; ?>" href="javascript:voteUp('<?php echo $args['course']['id']; ?>','<?php echo $args['comment_id']; ?>','<?php echo User::GetAuthenticatedID(); ?>','result');">
			<div class="voteUp">&nbsp;</div>
		</a>
		Course rating: <?php echo Comment::GetAttrib($args['comment_id'], 'rating'); ?>
	</p>
<?php	}	?>

<?php	/*****	SEARCH RESULTS	*****/	?>
<?php	if(($action == 'search' || $action == 'show') && $args['searchresults']) { // are there params?
			include("$ROOT/course/views/dataacquisition.pview.php"); ?>
<?php } ?>

<?php	/*****	COMMENTS	*****/	?>
<?php	$cid = $args['comment_id']; $jsid = uniqid(); $flags = 1; $text = 'Comment on "'.$args['course']['code'].'"'; ?>
<?php	$comments = $args['comments'];
		include("$ROOT/course/views/comment.pview.php");
?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

