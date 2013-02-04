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
	static protected $session;
	protected function setUp(){
		parent::setUp();
		if(self::$session == null){
			self::$session = new system\PholdBoxSessionManager();
		}
		//$GLOBALS["SESSION"] = new system\PholdBoxSessionManager();
		//$GLOBALS["SESSION"]->loadSession();
		$_COOKIE["PHPSESSID"] = "unitTests";
	}

	/**
	 * 
	 */
	public function testPushToSession(){
		self::$session->loadSession();
		self::$session->pushToSession("test", "1,2,3");
	}

	/**	
	 * 
	 * @depends testPushToSession
	 */
	public function testGetFromSession(){
		self::$session->loadSession();
		$this->assertEquals(self::$session->getFromSession("test"), "1,2,3");
		self::$session->delete();
	}
}
?>
