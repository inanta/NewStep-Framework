<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="Refresh" content="<?php echo $Time ?>; url=<?php echo $URL ?>" />
	<title><?php echo $Header ?></title>
	<style>
	    .common { width: 70%; border: #e0e0e0 1px solid; color: #666666; background-color: #f6f6f6; padding: 20px; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; }
	    #message { text-align: center; margin: 6.25% auto 1%; }
	    #message hr { height: 3px; border: 3px #E18A00 solid; width: 95%; }
	    #message div { padding: 10px 0 0 0; }
	    #message a { color: #666666; }
	    #message a:hover { color: #333333; }
	</style>
</head>
<body>
	<div class="common" id="message">
		<?php echo $Message ?>
		<hr />
		<div>
			<a href="<?php echo $URL ?>"><?php echo $IfNotReload ?></a>
		</div>
	</div>
</body>
</html>