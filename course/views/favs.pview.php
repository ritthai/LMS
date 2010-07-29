<div id="fav_courses"><p>Favourite courses:</p>
<?php	$favs = array_filter(	User::GetAttribs(User::GetAuthenticatedID()),
								function ($attr) { return $attr[0] == 'COURSEFAV'; }	);
		foreach($favs as $fav) {
			$id = $fav[1];
			$subj = ucfirst(Comment::GetSubject($fav[1]));
			echo "<p><a href=\"/search?id=$id\">$subj</a></p>";
		}
?>
</div>
<br>

<div id="fav_topics"><p>Favourite topics:<br>
<?php	$favs = array_filter(	User::GetAttribs(User::GetAuthenticatedID()),
								function ($attr) { return $attr[0] == 'TOPICFAV'; }	);
		foreach($favs as $fav) {
			$id = $fav[1];
			$subj = ucfirst(Comment::GetSubject($fav[1]));
			echo "<p>$subj</p>";
		}
?>
</div>
<br>
