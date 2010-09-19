<?php include("../includes/config.inc"); ?>
<html>
	<head>
		<title>Log</title>
		<style type="text/css">
			body {
				background-color: #000;
				color: #fff;
			}
			a {
				text-decoration: none;
				color: #f00;
			}
			.fatal {
				background-color: #f55;
			}
			.warn {
				background-color: #980;
			}
			.notice {
				background-color: #000;
			}
			.debug {
				background-color: #000;
				color: #0f0;
			}
			.fancybox {
				color: #000;
			}
			.hidden {
				display: none;
			}
			.overridehidden {
				display: inline !important;
			}
		</style>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"> </script>
		<script type="text/javascript" src="/fancybox/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
		<link rel="stylesheet" href="/fancybox/fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
	</head>
	<body>
<?php	$file = 'debug';
		if(isset($_GET['file'])) $file = $_GET['file'];
		echo syscall("tail -n 150000 $ROOT/admin/$file.html > $ROOT/admin/$file"."2.html");
		echo file_get_contents("$ROOT/admin/$file"."2.html");
?>
		<script type="text/javascript" src="../js/jquery.hotkeys-0.7.9.min.js"> </script>
		<script type="text/javascript">
			var cmdstate = '';
			jQuery(document).ready(function() {
				jQuery('.group').each(function (idx) {
					jQuery(this).fancybox();
				});
			});
			/*jQuery(document).bind('keydown', 'shift+;', function() {
				if(cmdstate == '') cmdstate = ':';
				alert(cmdstate);
			});
			jQuery(document).bind('keydown', 'g', function() {
				if(cmdstate == ':') cmdstate = '';
				alert(cmdstate);
				jQuery('.hidden').each(function(idx) {
					jQuery(this).toggle(
						function(){ jQuery(this).addClass("overridehidden"); },
						function(){ jQuery(this).removeClass("overridehidden"); }
						);
				});
			});*/
		</script>
	</body>
</html>
