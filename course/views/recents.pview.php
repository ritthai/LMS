<?php   /*****  RECENTLY VIEWED COURSES *****/  ?>
<?php	$recents = Pageview::ListAllForUser(get_viewer_id());
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
		<br>
						</div>
