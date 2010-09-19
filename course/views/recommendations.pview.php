<?php   /*****  RECOMMENDATIONS BASED ON PAGEVIEWS *****/  ?>
<?php	$other_users = Pageview::ListAllForPage($args['comment_id']);
		$unq_users = array();
		foreach($other_users as $ou) {
			$found = false;
			foreach($unq_users as $u) {
				if($ou['id'] == $u['id']) {
					$found = true;
					break;
				}
			}
			if(!$found) {
				$unq_users[] = $ou;
			}
		}
		$other_views = array();
		foreach($unq_users as $user) {
			$user_views = Pageview::ListAllForUser($user['id']);
			foreach($user_views as $view) {
				if($view['comment_id'] == $args['comment_id']) continue;
				if(isset($other_views[$view['comment_id']])) $other_views[$view['comment_id']]++;
				else $other_views[$view['comment_id']] = 1;
			}
		}
		arsort($other_views);
		if($other_views && count($other_views) > 0) {	?>
                        <div id="course_recommendations">
<?php	} else {	?>
                        <div id="course_recommendations" class="hidden">
<?php	}	?>
                            <div id="sidebar_course_recommendations">People who viewed this course also viewed:</div>
<?php   $ctr = 0;
		Error::setPrepend($other_views);
		Error::generate('debug', 'Recommendation array');
		foreach($other_views as $k=>$v) {
			if(++$ctr == 5) break; // limit to 5 top results
            $id = intval($k);
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
