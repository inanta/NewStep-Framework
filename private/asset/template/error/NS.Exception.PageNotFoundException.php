<?php header('HTTP/1.0 404 Not Found'); ?>
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
			<h2>
				404 Page Not Found<br />
				[<?php echo (strlen(NS_CURRENT_URL) > 50 ? substr(NS_CURRENT_URL, 0, 25) . ' ... ' . substr(NS_CURRENT_URL, -25) : NS_CURRENT_URL) ?>]
			</h2>
		</div>
	</div>
</body>
</html>