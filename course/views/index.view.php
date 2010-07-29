<?php
/**
	Given:	pagetitle,
			pageurl,
			actions
*/
?>

<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php	jsSetupAutocomplete('uni_form', 'uni', 'universities'); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

<p>ClassMate was built with one goal in mind: to supplement the end-to-end learning process. As students, we understand the importance of personal enrichment and the tremendous impact well-managed course related information can have. ClassMate leverages the power of the most prominent resource hubs available online in order to provide students with the most relevant, up-to-date and helpful information possible. ClassMate also provides a centralized information source which allows students to package, manage and utilize information on a course-by-course basis. </p>
<p>The power and elegance of ClassMate makes it easy for anyone to get started learning. </p>

<?php	//if(User::IsAuthenticated()) { /* USER AUTHENTICATED */ ?>
<p>	Enter the name of a university in the box below, or click here to pick a
	<a class="nicebodylink" href="/areas?country=<?php echo Country::GetID('Canada'); ?>">
		province.</a>
</p>
<?php		$args['actions']['subjects']->FORM_BEGIN('id="uni_form" class="search_form"'); ?>
				<div	style="background: url('/images/navigation/university.png') no-repeat;" >
					<input type="text" name="university" class="search_form" id="uni" size=22 maxlength=22
						onfocus="javascript:jQuery(this).parent().attr('style','background: url(\'/images/navigation/blank.png\') no-repeat'); "
						onblur="javascript:jQuery(this).parent().attr('style','background: url(\'/images/navigation/university.png\') no-repeat'); "
					/>
				</div>
<?php		$args['actions']['subjects']->FORM_END(); ?>
<?php	//} else { /* USER NOT AUTHENTICATED */ ?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

