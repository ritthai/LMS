<?php
/**
	Given:	pagetitle,
			actions,
*/
?>

<?php include("$TEMPLATEROOT/template_begin.inc"); ?>

		<script src="<?php echo "$HTMLROOT/js/prototype-1.6.0.3.js"; ?>" type="text/javascript"></script>
		<script type="text/javascript">
		/* From http://forums.macrumors.com/showthread.php?t=672301 :
		 * A pretty little hack to make uploads not hang in Safari. Just call this
		 * immediately before the upload is submitted. This does an Ajax call to
		 * the server, which returns an empty document with the "Connection: close"
		 * header, telling Safari to close the active connection. A hack, but
		 * effective. */
		function closeKeepAlive() {
			if (/AppleWebKit|MSIE/.test(navigator.userAgent)) {
				new Ajax.Request("/misc/empty.html", { asynchronous:false });
			}
		}
		</script>

<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

<?php	$args['actions']['upload']->FORM_BEGIN('enctype="multipart/form-data" onsubmit="closeKeepAlive();"');	?>
									<input type="hidden" name="MAX_FILE_SIZE" value="16777216" /> <!-- 16 MB -->
		Filename:					<input type="text" name="name" /><br/>
		<!--File to upload:-->		<input type="file" name="file" /><br/>
		Comment:					<textarea name="comment" rows="3" cols="40"></textarea><br/>
									<input type="submit" value="Upload" />
<?php	$args['actions']['upload']->FORM_END();	?>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>

