<?php
/*
 * Created on Dec 29, 2010
 *
 * PholdBox Config
 */

$SYSTEM = array();
$GLOBALS["SYSTEM"] = $SYSTEM;

//Debug Output
$SYSTEM["debug"] = true;

//Datasources
$SYSTEM["dbBatchSize"] = 100;
$SYSTEM["dsn"] = array();
$SYSTEM["dsn"]["default"] = "pholdbox";
// Add as many datasources as you like
// Pear DB syntax - phptype://username:password@protocol+hostspec/database
$SYSTEM["dsn"]["pholdbox"] = array();
//$SYSTEM["dsn"]["pholdbox"]["connection_string"] = "mysql://CS120:isfun@192.168.1.4/pholdbox";
$SYSTEM["dsn"]["pholdbox"]["connection_string"] = "mysql://CS120:isfun@localhost/pholdbox";

//Default Layout/view
$SYSTEM["default_layout"] = "layout.main";
$SYSTEM["default_event"] = "main.home"; 

//per app settings go here
$SYSTEM["app"]["mysetting"] = "PholdBox Rocks!";

?>
