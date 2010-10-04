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
								<div id="sidebar_recommendation_tagcloud_hidden" class="hidden">
<?php   $ctr = 0;
		Error::setPrepend($other_views);
		Error::generate('debug', 'Recommendation array');
		$reduced_recs = array(); $i = 0;
		foreach($other_views as $k=>$v) {
			$reduced_recs[$i++] = $v;
		} $n_rrecs = $i;
		Error::setPrepend($reduced_recs);
		Error::generate('debug', 'Reduced Recommendation array');
		$topn = 10; // limit to $topn top results
		foreach($other_views as $k=>$v) {
			if(++$ctr == $topn) break; // limit to $topn top results
            $id = intval($k);
            $subj = ucfirst(Comment::GetSubject($id));
			$crs = new CourseDefn( $subj );
			$success = $crs->load();
			$hrefid = $crs->id;
			// cloudinizr javascript is in template_end
			for($i=0; $i < $v/$reduced_recs[min(10,$n_rrecs-1)]; $i++) {
				echo strtoupper("$subj ");
			}
?>
<?php	} ?>
							</div>
							<div id="sidebar_recommendation_tagcloud">&nbsp;</div>
						</div>
<!--
							<div class="sidebar_fav_course_container">
								<a href="/search?id=<?php echo $hrefid; ?>">
									<div class="sidebar_fav_course">
										<?php echo $subj; ?>
									</div>
								</a>
								<div style="clear:both"></div>
							</div>
-->
