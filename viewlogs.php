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
				background-color: #ff0;
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
		<?php echo file_get_contents("admin/debug.html"); ?>
	</body>
</html>
