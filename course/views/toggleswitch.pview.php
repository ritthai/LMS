<?php /* $cid,$jsid,$flags,$comments,$text */ ?>
	<a href="javascript:toggleContentPanel(<?php echo "'$cid','$jsid','$flags'"; ?>);">
		<div class="expand" id="expand_<?php echo $jsid; ?>">&nbsp;</div>
		<div class="topic_name"><?php echo $text; ?></div>
	</a>
