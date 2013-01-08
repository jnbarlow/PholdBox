<?php
/*
 * Created on Jan 7, 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

class PholdBoxBaseObjTest extends PHPUnit_Framework_TestCase
{
	protected $baseObj;
	protected function setUp(){
		$_SERVER["SERVER_NAME"] = "pholdbox.local.dev";
		$GLOBALS["rc"] = array();
		require_once ("../../config/config.php");
		require_once ("../PholdBoxBaseObj.php");
		require_once ("../PhORM.php");
		require_once ("../PholdBoxSessionManager.php");
		$this->baseObj = new system\PholdBoxBaseObj();
	}

	public function testDebug(){
		$path = $this->baseObj->debug("main.home");
		$this->assertEquals($GLOBALS["SYSTEM"]["debugger"]["userStack"][0]["name"], "unnamed");
		$this->assertEquals($GLOBALS["SYSTEM"]["debugger"]["userStack"][0]["object"], "main.home");		
	}
	
	public function testSetSessionValue(){
		//$GLOBALS["SESSION"] = new system\PholdBoxSessionManager();
	}

}
?>
