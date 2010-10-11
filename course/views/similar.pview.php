<?php   /*****  RECOMMENDATIONS BASED ON SIMILARITY *****/  ?>
<?php	$crs = new CourseDefn($args['course']['id']);
		$crs->load();
		$similarities = $crs->getSimilar();
		arsort($similarities);
		if($similarities && count($similarities) > 0) {	?>
                        <div id="course_similarities">
<?php	} else {	?>
                        <div id="course_similarities" class="hidden">
<?php	}	?>
                            <div id="sidebar_course_similarities">Similar courses:</div>
								<div id="sidebar_similarities_tagcloud_hidden" class="hidden">
<?php   $ctr = 0;
		Error::setPrepend($similarities);
		Error::generate('debug', 'Similarity array');
		$topn = 10; // limit to $topn top results
		foreach($similarities as $k=>$v) {
			if(++$ctr == $topn) break; // limit to $topn top results
            $id = intval($k);
            $subj = ucfirst(Comment::GetSubject($id));
			$crs = new CourseDefn($subj);
			$crs->load();
			$hrefid = $crs->id;
			// cloudinizr javascript is in template_end
			for($i=0; $i < (float)($similarities[$k])*10; $i++) {
				echo strtoupper("$subj ");
			}
?>
<?php	} ?>
							</div>
							<div id="sidebar_similarities_tagcloud">&nbsp;</div>
						</div>
