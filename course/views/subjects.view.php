<?php
/**
	Given:	pagetitle,
			pageurl,
			subjects :	title,
						code
			actions
*/
?>

<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

<div style="float: left; width: 45%">
<p class="subjects">
<?php
	$prev_letter = ' ';
	if($args['subjects']) {
		$switched_cols = false; $c = 0;
		foreach($args['subjects'] as $subject) {
			if($prev_letter == $subject['title'][0]) {
				$first_letter = $prev_letter;
				$begin = "";
				$end = "<br>";
			} else {
				$first_letter = $subject['title'][0];
				$begin = "</p><p class=\"subjects\">";
				$end = "<br>";
				if(!$switched_cols && $c >= count($args['subjects'])/2) {
					$switched_cols = true;
					$begin = "</p></div><div style=\"float: right; width: 45%\"><p class=\"subjects\">";
					$end = "";
				}
			}
			$c++;
			$title_rest = substr($subject['title'], 1);
			$formatted_out = "$first_letter$title_rest ($subject[code])";
			$formatted_out = str_replace("Jewish", "<br>Jewish", $formatted_out);
?>
<?php echo $begin; ?>
	<a class="bodylink" href="/subject?<?php echo "code=$subject[code]&university={$args['university']['id']}"; ?>">
		<?php echo $formatted_out; ?>
	</a>
<?php echo $end; ?>
<?php		$prev_letter = $subject['title'][0];
		}
	} else { ?>
<p>No courses for this university.</p>
<?php } ?>
</p>
</div>
<div style="clear:both"> </div>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

