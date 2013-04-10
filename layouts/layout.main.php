<?php
/*
 * Created on May 12, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>PholdBox - <?php echo $rc["PB_VERSION"]?></title>
		<style>
			@import "includes/styles/styles.css";
		</style>
	</head>
	<body>
		<div id="content-container">
			<div id="header">
				<h1>Welcome to PholdBox</h1>
				<h4>Version <?php echo $rc["PB_VERSION"]?></h1>
			</div>
			<div id="content">
				<?php include($view);?>			
			</div>
		</div>
	</body>
</html>
