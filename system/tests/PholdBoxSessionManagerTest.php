<?php
/*
 * Created on Jan 7, 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("PholdBoxTestBase.php");
class PholdBoxSessionManagerTest extends PholdBoxTestBase
{
	protected $session;
	protected function setUp(){
		parent::setUp();
		$this->session = new system\PholdBoxSessionManager();
		$_COOKIE["PHPSESSID"] = "unitTests";
	}
	
	protected function tearDown(){
		$this->session->delete();
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testPushToSession(){
		$this->session->loadSession();
		$this->session->pushToSession("test", "1,2,3");
	}

	/**	
	 * @runInSeparateProcess
	 * @depends testPushToSession
	 */
	public function testGetFromSession(){
		$this->session->loadSession();
		$this->assertEquals($this->session->getFromSession("test"), "1,2,3");
	}
}
?>
