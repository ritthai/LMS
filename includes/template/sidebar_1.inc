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
<?php   if(!User::AuthenticatedUserHasMatchingAttrib('coursefav', $args['comment_id'])) { ?>
<?php	if($args['comment_id']) { ?>
                        <div class="sidebar_add_to_favs_container" id="favs_<?php echo $args['comment_id']; ?>">
                            <a  class="bodylink add_to_favs"
                                id="add_to_favs_<?php echo $args['comment_id']; ?>"
                                href="javascript:addToFavs('<?php echo $args['comment_id']; ?>','<?php echo User::GetAuthenticatedID(); ?>','course','<?php echo $args['course']['code']; ?>');">
                                <div class="sidebar_add_to_favs">Add to Favs</div>
                                <div class="addToFavs" style="float:right">&nbsp;</div>
                            </a>
                        </div>
<?php	} ?>
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
                        <div id="fav_courses">
                            <div id="sidebar_fav_courses">Favourite courses:</div>
<?php   $favs = array_filter(   User::GetAttribs(User::GetAuthenticatedID()),
                                function ($attr) { return $attr[0] == 'COURSEFAV'; }    );
        foreach($favs as $fav) {
            $id = $fav[1];
            $subj = ucfirst(Comment::GetSubject($fav[1])); ?>
							<div class="sidebar_fav_course_container">
								<a  class="bodylink rm_from_favs"
								    id="rm_from_favs_<?php echo $fav[1]; ?>"
								    href="javascript:rmFromFavs('<?php echo $fav[1]; ?>','<?php echo User::GetAuthenticatedID(); ?>','course','<?php echo $subj; ?>');">
									<div class="rmFromFavs sidebar_rm_from_favs">&nbsp;</div>
								</a>
								<a href="/search?id=<?php echo $id; ?>">
									<div class="sidebar_fav_course">
										<?php echo $subj; ?>
									</div>
								</a>
								<div class="addToFavs sidebar_add_to_favs">&nbsp;</div>
								<div style="clear:both"></div>
							</div>
<?php	} ?>
						</div>
