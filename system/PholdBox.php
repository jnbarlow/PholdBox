<?php

require("config/config.php");
require("RCProcessor.php");
require("PholdBoxBaseObj.php");
require("Event.php");
require("PhORM.php");

$VERSION = "1.0 beta";

$RCProcessor = new system\RCProcessor();
$GLOBALS["rc"] = $RCProcessor->getCollection();
$GLOBALS["rc"]["PB_VERSION"] = $VERSION;

$event = new system\Event($SYSTEM["default_layout"]);
if(array_key_exists("event", $GLOBALS["rc"]))
{
	$event->processEvent($GLOBALS["rc"]["event"]);
}
else
{	
	$event->processEvent($SYSTEM["default_event"]);
}
?>
