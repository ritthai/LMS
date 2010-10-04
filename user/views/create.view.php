<?php
/**
	Given:	pagetitle,
			actions,
			recaptcha_error
*/

?>
<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php   jsSetupAutocomplete(''/* don't submit form */, 'university', 'universities'); ?>
<script src="<?php echo $HTMLROOT; ?>/js/jquery.validate.js"> </script>
<script type="text/javascript">
jQuery(document).ready(function() {
	// http://docs.jquery.com/Plugins/validation
	// http://jquery.bassistance.de/validate/demo/milk/
	jQuery("#create_form").validate({
		rules: {
			//firstname: "required",
			//lastname: "required",
			name: {
				required: true,
				minlength: 2,
				remote: "/autocomplete?list=users"
			},
			university: {
				required: true,
				remote: "/autocomplete?list=universities"
			},
			gradyear: {
				required: false
			},
			password: {
				required: true,
				minlength: 8
			},
			password_confirm: {
				required: true,
				minlength: 8,
				equalTo: "#password"
			},
			email: {
				required: true,
				email: true,
			},
			terms: "required"
		},
		messages: {
			firstname: "Enter your first name",
			lastname: "Enter your last name",
			name: {
				required: "Enter a username",
				minlength: jQuery.format("Enter at least {0} characters"),
				remote: jQuery.format("{0} is already in use")
			},
			university: {
				required: "Select a university",
				remote: jQuery.format("{0} is not a supported university")
			},
			password: {
				required: "Provide a password",
				rangelength: jQuery.format("Enter at least {0} characters")
			},
			password_confirm: {
				required: "Repeat your password",
				minlength: jQuery.format("Enter at least {0} characters"),
				equalTo: "Passwords do not match"
			},
			email: {
				required: "Please enter a valid email address",
				minlength: "Please enter a valid email address",
				remote: jQuery.format("{0} is already in use")
			},
			terms: "You must accept the Terms of Use"
		},
		/*invalidHandler: function(form, validator) {
			var errors = validator.numberOfInvalids();
			if (errors) {
				var message = errors == 1
					? 'You missed 1 field. It has been highlighted'
					: 'You missed ' + errors + ' fields. They have been highlighted';
				jQuery("div.error span").html(message);
				jQuery("div.error").show();
			} else {
				jQuery("div.error").hide();
			}
		},*/
		// the errorPlacement has to take the table layout into account
		errorPlacement: function(error, element) {
			if(element.is(":radio"))
				error.appendTo(element.parent().next().next());
			else if(element.is(":checkbox"))
				error.appendTo(element.next());
			else
				error.appendTo(element.parent().next());
		},
		submitHandler: function(form) {
			form.submit();
		},
		success: function(label) {
			// set &nbsp; as text for IE
			label.html("&nbsp;").addClass("checked");
		},
	});
});
</script>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

<?php $args['actions']['create']->FORM_BEGIN("id=\"create_form\" autocomplete=\"on\""); ?>

	<table>
	<tr>
		<td class="label"><label for="name">Username</label><em>*</em></td>
		<td class="field"><input type="text" id="name" name="name" value="<?php echo $args['name']; ?>" /></td>
		<td class="status"></td>
	</tr>
	<tr>
		<td class="label"><label for="firstname">First name</label></td>
		<td class="field"><input type="text" id="firstname" name="firstname" value="<?php echo $args['firstname']; ?>" /></td>
		<td class="status"></td>
	</tr>
	<tr>
		<td class="label"><label for="lastname">Last name</label></td>
		<td class="field"><input type="text" id="lastname" name="lastname" value="<?php echo $args['lastname']; ?>" /></td>
		<td class="status"></td>
	</tr>
	<tr>
		<td class="label"><label for="university">University</label><em>*</em></td>
		<td class="field"><input type="text" id="university" name="university" style="color: #000" value="<?php echo $args['university']; ?>" /></td>
		<td class="status"></td>
	</tr>
	<tr>
		<td class="label"><label for="gradyear">Year of Graduation</label></td>
		<td class="field"><input type="text" id="gradyear" name="gradyear" value="<?php echo $args['gradyear']; ?>" /></td>
		<td class="status"></td>
	</tr>
	<tr>
		<td class="label"><label for="email">Email address</label><em>*</em></td>
		<td class="field"><input type="text" id="email" name="email" value="<?php echo $args['email']; ?>" /></td>
		<td class="status"></td>
	</tr>
	<tr>
		<td class="label"><label for="password">Password</label><em>*</em></td>
		<td class="field"><input type="password" id="password" name="password" /></td>
		<td class="status"></td>
	</tr>
	<tr>
		<td class="label"><label for="password_confirm">Confirm Password</label><em>*</em></td>
		<td class="field"><input type="password" id="password_confirm" name="password_confirm" /></td>
		<td class="status"></td>
	</tr>
<?php	if(User::HasPermissions('admin')) { ?>
	<tr>
		<td class="label"><label for="role">Role</label></td>
		<td class="field"><input type="text" id="role" name="role" value="banned,admin" /></td>
		<td class="status"></td>
	</tr>
<?php	} ?>
	<tr>
		<td></td>
		<td colspan="2">
			<input id="terms" type="checkbox" name="terms" />
			<label for="terms">I have read and accept the terms of use.</label>
		</td>
	</tr>
	<tr>
		<td></td>
		<td colspan="2">
			<?php echo recaptcha_get_html($CONFIG['recaptcha_pubkey'], $args['recaptcha_error']); ?>
		</td>
	</tr>
	<tr>
		<td class="label"></td>
		<td class="field"><input type="submit" value="Create account" class="submit" /></td>
		<td class="status"></td>
	</tr>
	</table>
<?php $args['actions']['create']->FORM_END(); ?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

