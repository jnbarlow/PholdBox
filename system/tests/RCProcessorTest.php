<?php
/*
 * Created on Jan 7, 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

class RCProcessorTest extends PHPUnit_Framework_TestCase
{
	protected function setUp(){
		require_once ("system/RCProcessor.php");
	}

	public function testGetCollection(){
		$rcp = new system\RCProcessor();
		$return = $rcp->getCollection();
		$this->assertEmpty($return);
	}
	
	public function testGetCollection_Get(){
		$_GET["test"] = "1,2,3";
		$rcp = new system\RCProcessor();
		$return = $rcp->getCollection();
		$this->assertEquals($return["test"], "1,2,3");
	}

	public function testGetCollection_Post(){
		$_POST["test"] = "1,2,3";
		$rcp = new system\RCProcessor();
		$return = $rcp->getCollection();
		$this->assertEquals($return["test"], "1,2,3");
	}
	
	public function testGetCollection_PostGetOverride(){
		$_GET["test"] = "1,2,3";
		$_GET["test"] = "4,5,6";
		$rcp = new system\RCProcessor();
		$return = $rcp->getCollection();
		$this->assertEquals($return["test"], "4,5,6");
	}
	
}
?>
