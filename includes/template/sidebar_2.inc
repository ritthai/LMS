                        <p><a class="loginlink" href="#login_form">My options</a></p>
                        <p><a class="loginlink" href="#login_form">My profile</a></p>
                        <p>
<?php   /*****  ADD COURSE TO FAVOURITES    *****/  ?>
<?php   if(!User::AuthenticatedUserHasMatchingAttrib('coursefav', $subject['comment_id'])) { ?>
                            <div id="favs_<?php echo $args['comment_id']; ?>">
                                <a  class="bodylink add_to_favs"
                                    id="add_to_favs_<?php echo $args['comment_id']; ?>"
                                    href="javascript:addToFavs( '<?php echo $args['comment_id']; ?>',
											                    '<?php echo User::GetAuthenticatedID(); ?>',
																'course',
															    '<?php echo $args['course']['code']; ?>' );">
                                    <div class="addToFavs">&nbsp;</div>
                                    Add to Favs
                                </a>
                            </div>
<?php   } ?>
                        </p>
<?php   /*****  PRIVATE MESSAGES    *****/  ?>
                        <p>
                            <div class="privateMessages">&nbsp;</div>
                            Private Messages<br>
                        </p>
