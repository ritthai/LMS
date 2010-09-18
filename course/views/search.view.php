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
	<p id="description"><?php echo clean($args['course']['descr']); ?></p>
<?php	}	?>

<?php	/*****	SEARCH RESULTS	*****/	?>
<?php	if(($action == 'search' || $action == 'show') && $args['searchresults']) { // are there params?
			include("$ROOT/course/views/dataacquisition.pview.php"); ?>
<?php } ?>

<?php	/*****	COMMENTS	*****/	?>
<?php	$boxid = $args['comment_id'] + 1000000000; ?>
<div class="search_result">
	<a href="javascript:toggleContentPanel(<?php echo $boxid; ?>);">
		<div class="expand" id="expand_<?php echo $boxid; ?>">&nbsp;</div>
		<div class="topic_name">Comment on <?php echo $args['course']['code']; ?></div>
	</a>
	<div class="all_result_content" id="all_result_content_<?php echo $boxid; ?>">
<?php	$cid = $args['comment_id']; $comments = $args['comments'];
		include("$ROOT/course/views/comment.pview.php");
?>
	</div>
</div>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

