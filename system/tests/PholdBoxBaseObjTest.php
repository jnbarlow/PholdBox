<?php
/*
 * Created on Jan 7, 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("PholdBoxTestBase.php");
class PholdBoxBaseObjTest extends PholdBoxTestBase
{
	protected $baseObj;
	protected function setUp(){
		parent::setUp();
		$this->baseObj = new system\PholdBoxBaseObj();
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testDebug(){
		$path = $this->baseObj->debug("main.home");
		$this->assertEquals($GLOBALS["SYSTEM"]["debugger"]["userStack"][0]["name"], "unnamed");
		$this->assertEquals($GLOBALS["SYSTEM"]["debugger"]["userStack"][0]["object"], "main.home");		
	}
}
?>
