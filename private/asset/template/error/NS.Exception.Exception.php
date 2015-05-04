<?php header('HTTP/1.0 500 Internal Server Error'); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>NewStep Framework | Error</title>
	<style>
	    .common { width: 70%; border: #e0e0e0 1px solid; color: #666666; background-color: #f6f6f6; padding: 20px; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; }
	    #message { text-align: center; margin: 6.25% auto 1%; }
	    #message hr { height: 3px; border: 3px #E18A00 solid; width: 95%; }
	    #trace { text-align: left; margin: 10px auto; }
	    #output { text-align: left; margin: 10px auto; }
	</style>
</head>
<body>
	<div>
		<div class="common" id="message">
			<h1>NewStep Framework</h1>
			<hr />
			<h2><?php echo $ErrorHeader; ?></h2>
			<h3>
				<?php echo $NSErrorMessageCaption; ?>: <?php echo $Message ?><br />
				<?php echo $ExceptionCaption ?>: <?php echo $Source ?><br />
				<?php echo $FileCaption ?>: <?php echo $File ?> line <?php echo $Line ?><br />
			</h3>
			<div>NewStep Framework Version: <?php echo NS_VERSION / 100 ?> - <?php echo NS_VERSION_NAME ?><br /></div>
			<div><a href="<?php echo NS_SITE ?>">NewStep Framework Website</a></div>
		</div>
		<?php if($LastOutput): ?>
		<div class="common" id="output">
			<h3>Last Output From Buffer:</h3>
			<?php echo $LastOutput ?>
		</div>
		<?php endif ?>
		<?php if(NS_DEBUG_MODE): ?>
		<div class="common" id="trace">
			<?php $counter = 1 ?>
			<?php foreach($Trace as $x): ?>
				<?php if(isset($x['file'])): ?>
				<div><?php echo $TraceCaption, ' ', $counter++; ?>: <?php echo $x['file'] ?> line <?php echo $x['line'] ?></div>
				<?php endif ?>
			<?php endforeach ?>
		</div>
		<?php endif ?>
	</div>
</body>
</html>