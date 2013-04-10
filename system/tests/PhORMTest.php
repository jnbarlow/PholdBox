<?php
/*
 * Created on Jan 7, 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("PholdBoxTestBase.php");

class PhORMTest extends PholdBoxTestBase
{
	static protected $myObj;
	static protected $id;
		
	protected function setUp()
	{
		parent::setUp();
		include_once("model/MyObj.php");
		if(self::$myObj == null)
		{
			self::$myObj = new MyObj();
		}
	}
	
	public function tearDown()
	{
		self::$myObj->clear();
	}
	
	public static function tearDownAfterClass()
	{
		self::$myObj = null;	
	}

	/**
	 * 
	 */
	public function testSave_insert()
	{
		self::$myObj->setName("test");
		self::$myObj->setTitle("King");
		self::$myObj->save();
		self::$id = self::$myObj->getId();
		$this->assertNotEmpty(self::$myObj->getId());	
	}
	
	/**
	 * 
	 * @depends testSave_insert
	 */
	public function testLoad()
	{
		$this->assertEmpty(self::$myObj->getName());		
		self::$myObj->setId(self::$id);
		self::$myObj->load();
		$this->assertEquals(self::$myObj->getTitle(), "King");	
	}
	
	/**
	 * 
	 * @depends testSave_insert
	 */
	public function testSave_update()
	{
		self::$myObj->setId(self::$id);
		self::$myObj->setName("test");
		self::$myObj->setTitle("King1");
		self::$myObj->save();
		self::$myObj->clear();
		self::$myObj->setId(self::$id);
		self::$myObj->load();
		$this->assertEquals(self::$myObj->getTitle(), "King1");	
	}
	
	/**
	 * 
	 * @depends testLoad
	 */
	public function testDelete()
	{
		self::$myObj->setId(self::$id);
		self::$myObj->delete();
		self::$myObj->setId(self::$id);
		self::$myObj->load();
		$this->assertEmpty(self::$myObj->getTitle());
	}
}
?>
