<?php
/**
	Given:	pagetitle,
			actions,
*/
?>
<html>
	<head>
		<title><?php echo $args['pagetitle']; ?></title>
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
	</head>
	<body>
<?php   if($errors=Error::get()) {  ?>
        <div style="border: 1px solid #F00; background-color: #fff5f5;">
            <ul>
            <?php foreach($errors as $error) {  ?>
                <li><?php echo Error::format_error($error); ?></li>
            <?php } ?>
            </ul>
        </div>
<?php   }   ?>

<?php	$args['actions']['upload']->FORM_BEGIN('enctype="multipart/form-data" onsubmit="closeKeepAlive();"');	?>
									<input type="hidden" name="MAX_FILE_SIZE" value="4194304" /> <!-- 4 MB -->
		<!--File to upload:-->		<input type="file" name="file" /><br/>
									<input type="submit" value="Upload" />
<?php	$args['actions']['upload']->FORM_END();	?>
	</body>
</html>
