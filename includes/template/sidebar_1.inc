						<div id="sidebar_realname"><?php echo User::GetAuthenticatedAttrib('realname'); ?></div>
                        <div id="sidebar_university"> <?php echo User::GetAuthenticatedAttrib('university'); ?>
                        <?php echo User::GetAuthenticatedAttrib('gradyear'); ?></div>
						<div id="sidebar_options_and_profile">
							<div id="sidebar_avatar">
								<div id="sidebar_user_image">
									<img src="<?php echo User::GetAvatar(User::GetAuthenticatedID()); ?>" />
								</div>
							</div>
							<div id="sidebar_options_and_profile_text">
								<a href="/user/"><div id="sidebar_options">My options</div></a>
								<a href="/user/"><div id="sidebar_profile">My profile</div></a>
							</div>
						</div>
						<div style="clear:both"> </div>
						<br>
<?php   /*****  ADD COURSE TO FAVOURITES    *****/  ?>
<?php	$favs = User::GetAttrib(User::GetAuthenticatedID(), 'COURSEFAV');
		$display_add_to_favs = $args['comment_id'];
		foreach($favs as $fav) {
			if($args['comment_id'] == $fav) $display_add_to_favs = false;
		}
		if($display_add_to_favs) {
?>
                        <div class="sidebar_add_to_favs_container" id="favs_<?php echo $args['comment_id']; ?>">
                            <a  class="bodylink add_to_favs"
                                id="add_to_favs_<?php echo $args['comment_id']; ?>"
                                href="javascript:addToFavs('<?php echo $args['comment_id']; ?>','<?php echo User::GetAuthenticatedID(); ?>','course','<?php echo $args['course']['code']; ?>');">
                                <div class="sidebar_add_to_favs">Add to Favs</div>
                                <div class="addToFavs" style="float:right">&nbsp;</div>
                            </a>
                        </div>
<?php   } ?>
						<div style="clear:both"> </div>
						<br>
						<div style="clear:both"> </div>
<?php   /*****  PRIVATE MESSAGES    *****/  ?>
                        <div id="sidebar_private_messages_box">
                            <div id="sidebar_private_messages">&nbsp;Private Messages</div>
                            <div class="privateMessages">&nbsp;</div>
                        </div>
						<div style="clear:both"> </div>
                        <div id="sidebar_message_totals">0 Unread, Total 3</div>
						<div style="clear:both"> </div>
						<br>
<?php   /*****  TRACKED COURSES *****/  ?>
<?php	if($favs && count($favs) > 0) {	?>
                        <div id="fav_courses">
<?php	} else {	?>
                        <div id="fav_courses" class="hidden">
<?php	}	?>
                            <div id="sidebar_fav_courses">Favourite courses:</div>
<?php   natsort($favs);
		foreach($favs as $fav) {
            $id = intval($fav);
            $subj = ucfirst(Comment::GetSubject($id));
			$crs = new CourseDefn( $subj );
			$success = $crs->load();
			$hrefid = $crs->id;
?>
							<div class="sidebar_fav_course_container">
								<a  class="bodylink rm_from_favs"
								    id="rm_from_favs_<?php echo $id; ?>"
								    href="javascript:rmFromFavs('<?php echo $id; ?>','<?php echo User::GetAuthenticatedID(); ?>','course','<?php echo $subj; ?>');">
									<div class="rmFromFavs sidebar_rm_from_favs">&nbsp;</div>
								</a>
								<a href="/search?id=<?php echo $hrefid; ?>">
									<div class="sidebar_fav_course">
										<?php echo $subj; ?>
									</div>
								</a>
								<div class="addToFavs sidebar_add_to_favs">&nbsp;</div>
								<div style="clear:both"></div>
							</div>
<?php	} ?>
						</div>

<br>

<?php   /*****  RECENTLY VIEWED COURSES *****/  ?>
<?php	$recents = Pageview::ListAllForUser(User::GetAuthenticatedID());
		Error::generate('debug', $recents);
		if($recents && count($recents) > 0) {	?>
                        <div id="recent_courses">
<?php	} else {	?>
                        <div id="recent_courses" class="hidden">
<?php	}	?>
                            <div id="sidebar_recent_courses">Recent courses:</div>
<?php   $ctr=0;
		$unique_recents = array();
		foreach($recents as $r) {
			$found = false;
			foreach($unique_recents as $u) {
				if($u['comment_id'] == $r['comment_id']) {
					$found = true;
					break;
				}
			}
			if(!$found) {
				$unique_recents[] = $r;
				if(++$ctr == 5) break; // limit to most recent 5 courses
			}
		}
		foreach($unique_recents as $recent) {
            $id = intval($recent['comment_id']);
            $subj = ucfirst(Comment::GetSubject($id));
			$crs = new CourseDefn( $subj );
			$success = $crs->load();
			$hrefid = $crs->id;
?>
							<div class="sidebar_fav_course_container">
								<a href="/search?id=<?php echo $hrefid; ?>">
									<div class="sidebar_fav_course">
										<?php echo $subj; ?>
									</div>
								</a>
								<div style="clear:both"></div>
							</div>
<?php	} ?>
						</div>

<br>

<?php	include("$ROOT/course/views/recommendations.pview.php"); ?>
