<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>NewStep Framework | Error</title>
	<style>
	    .message { width: 70%; margin: 6.25% auto; text-align: center; border: #e0e0e0 1px solid; color: #666666; background-color: #f6f6f6; padding: 20px; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; }
	    .message hr { height: 3px; border: 3px #E18A00 solid; width: 95%; }
	</style>
</head>
<body>
	<div>
		<div class="message">
			<h1>NewStep Framework</h1>
			<hr />
			<h2>PHP Fatal Error</h2>
			<h3>
				NS Error Message: <?php echo $Message ?><br />
				File: <?php echo $File ?> line <?php echo $Line ?><br />
			</h3>
			<div>NewStep Framework Version: <?php echo NS_VERSION / 100 ?> - <?php echo NS_VERSION_NAME ?><br /></div>
			<div><a href="<?php echo NS_SITE ?>">NewStep Framework Website</a></div>
		</div>
	</div>
</body>
</html>