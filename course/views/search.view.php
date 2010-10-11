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
<?php	$cid = $args['comment_id']; $jsid = uniqid(); $flags = 1; $text = 'Comment on "'.$args['course']['code'].'"'; ?>
<?php	$comments = $args['comments'];
		include("$ROOT/course/views/comment.pview.php");
?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

