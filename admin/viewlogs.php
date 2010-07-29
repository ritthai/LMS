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
		<?php echo syscall("tail -n 20000 $ROOT/admin/debug.html > $ROOT/admin/debug2.html"); ?>
		<?php echo file_get_contents("$ROOT/admin/debug2.html"); ?>
	</body>
</html>
