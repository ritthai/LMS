<?php
/**
	Given:	...
*/
?>

<!-- /* ##### Beginning of template_begin ##### */ -->
<?php include('template_begin.inc'); ?>
<!-- /* ##### End of template_begin ##### */ -->

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

<!-- /* ##### Beginning of template_notices ##### */ -->
<?php include('template_notices.inc'); ?>
<!-- /* ##### End of template_notices ##### */ -->

<!-- /* ##### Beginning of template_end ##### */ -->
<?php include('template_end.inc'); ?>
<!-- /* ##### End of template_end ##### */ -->

