<html>
	<head>
		<title>Log</title>
		<style type="text/css">
			body {
				background-color: #000;
				color: #fff;
			}
			.fatal {
				background-color: #f00;
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
		</style>
	</head>
	<body>
		<?php exec('tail -n 250 admin/debug.html > admin/debug2.html;
					mv admin/debug2.html admin/debug.html'); ?>
		<?php echo file_get_contents("admin/debug.html"); ?>
	</body>
</html>
