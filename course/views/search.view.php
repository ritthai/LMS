<?php
/**
	Given:	pagetitle,
			pageurl,
			course:	title,
					code,
					descr
			comments: id,
			searchresults,
			actions
*/
?>

<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

<?php	/*****	SEARCH AREA	*****/	?>
<?php $args['actions']['search']->FORM_BEGIN(); ?>
	Enter the course code:	<input type="text" id="terms" name="terms" value="<?php echo $args['course']['code']; ?>" /><br/>
							<input type="submit" value="Find resources" />
<?php $args['actions']['search']->FORM_END(); ?>

<?php	/*****	COURSE DESCRIPTION	*****/	?>
<?php   if($args['actions']['search']->wasCalled()) {  ?>
	Description:			<textarea cols=40 rows=5 id="descr" name="descr" /><?php echo $args['course']['descr']; ?></textarea><br/>
<?		}	?>

<?php	/*****	SEARCH RESULTS	*****/	?>
<?php	if($args['actions']['search']->wasCalled()) { // are there params?
			include('views/dataacquisition.view.php');
		}
?>

<hr/>

<?php	/*****	COMMENTS	*****/	?>
<?php	if($args['comments']) {
			foreach($args['comments'] as $id) {	?>

	<div style="border: 1px solid #000; background-color: #ddf">
		<h5><?php echo Comment::GetSubject($id); ?>
				- Posted at <?php echo Comment::GetTimestamp($id); ?>
				by <?php echo User::GetAttrib(Comment::GetAttrib($id, 'owner'), 'name'); ?>
		</h5>
		<p>
			<?php echo Comment::GetAttrib($id, 'body'); ?>
		</p>
	</div>

<?php		}	?>
<?php	}	?>

<?php	/***** POST A COMMENT *****/	?>
<?php $args['actions']['post']->FORM_BEGIN(); ?>
	Leave a comment:															<br/>
	Subject:				<input type="text" name="subject" value="" />		<br/>
	Body:					<textarea cols=40 rows=5 name="body" /></textarea>	<br/>
							<input type="hidden" name="course" value="<?php echo $args['course']['code']; ?>" />
							<input type="submit" value="Post" />
<?php $args['actions']['post']->FORM_END(); ?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

