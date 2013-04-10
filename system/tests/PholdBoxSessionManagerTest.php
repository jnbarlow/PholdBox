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
		$_COOKIE["PHPSESSID"] = "unitTests";
		$_SERVER["REMOTE_ADDR"] = "128.0.0.1";
		$_SERVER["HTTP_USER_AGENT"] = "unitTests";
		$_COOKIE["CHECKSUM1"] = hash("sha1", $_SERVER["REMOTE_ADDR"]);
		$_COOKIE["CHECKSUM2"] = hash("sha1", $_SERVER["HTTP_USER_AGENT"]);
		self::$session->clear();
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
	public function testGetFromSession_phish(){
		$_SERVER["REMOTE_ADDR"] = "128.0.0.2";
		self::$session->loadSession();
		$this->assertEquals("", self::$session->getFromSession("test"));
	}
	
	/**	
	 * 
	 * @depends testGetFromSession_phish
	 */
	public function testGetFromSession_phish2(){
		$_SERVER["HTTP_USER_AGENT"] = "unitTests1";
		self::$session->loadSession();
		$this->assertEquals("", self::$session->getFromSession("test"));
	}
	
	/**	
	 * 
	 * @depends testGetFromSession_phish2
	 */
	public function testGetFromSession(){
		self::$session->loadSession();
		$this->assertEquals("1,2,3", self::$session->getFromSession("test"));
		self::$session->delete();
	}
}
?>
