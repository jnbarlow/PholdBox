<?php
/*
 * Created on Dec 29, 2010
 *
 * PholdBox Config
 */

$SYSTEM = array();
$GLOBALS["SYSTEM"] = &$SYSTEM;

//-- Production values --

//Debug Output
$SYSTEM["debug"] = true;

//Datasources
$SYSTEM["dbBatchSize"] = 100;
$SYSTEM["dsn"] = array();
$SYSTEM["dsn"]["default"] = "pholdbox";
// Add as many datasources as you like
// The connection string is an array that takes the PDO connection string, username, and password
// PDO DB syntax: array("dbtype:host=<hostname>;dbname=<dbname>", "username", "password")
$SYSTEM["dsn"]["pholdbox"] = array();
$SYSTEM["dsn"]["pholdbox"]["connection_string"] = array("mysql:host=localhost;dbname=pholdbox", "root", "root");

//Default Layout/view
$SYSTEM["default_layout"] = "layout.main";
$SYSTEM["default_event"] = "main.home"; 

//per app settings go here
$SYSTEM["app"]["mysetting"] = "PholdBox Rocks!";

//-- Site Specific Configs --
//example Site Specific: Override any setting by prepending the hostname
$SYSTEM["test.local.dev"]["dsn"]["pholdbox"]["connection_string"] = array("mysql:host=localhost;dbname=pholdbox", "someother", "credentials");
