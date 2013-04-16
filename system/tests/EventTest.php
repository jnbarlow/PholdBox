<?php
/*
 * Created on Jan 7, 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("PholdBoxTestBase.php");
class EventTest extends PholdBoxTestBase
{
	protected $event;
	protected function setUp(){
		parent::setup();
		$SYSTEM["debug"] = false;
	}
	
	function testProcessEvent(){
		$this->event = new system\Event("layout.main");
		ob_start();
		$this->event->processEvent("main.home");
		$this->assertContains("Welcome", ob_get_clean());
	}
	
	function testInvalidHandler(){
		$this->event = new system\Event("layout.main");
		ob_start();
		$this->event->processEvent("foo.bar");
		$this->assertEquals(ob_get_clean(), "Invalid Handler: foo.bar");
	}
	
	function testMissingHandlerFunction(){
		$this->event = new system\Event("layout.main");
		ob_start();
		$this->event->processEvent("main.bar");
		$this->assertEquals(ob_get_clean(), "Call fired: bar<br>");
	}
	
	function testDebugOutput(){
		$SYSTEM["debug"] = true;
		$this->event = new system\Event("layout.main");
		ob_start();
		$this->event->renderDebugger();
		$this->assertContains("Debugger", ob_get_clean());
	}
	
	function testSetLayout(){
		$this->event = new system\Event("layout.main");
		$this->event->setLayout("layout.main");
		ob_start();
		$this->event->processEvent("main.home");
		$this->assertContains("Welcome", ob_get_clean());
	}
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	function testSetLayout_invalidFile(){
		$this->event = new system\Event("layout.main");
		$this->event->setLayout("layout.bleh");
		$this->event->processEvent("main.home");
	}
	
	function testSetValue_GetValue(){
	 	$this->event = new system\Event("layout.main");
	 	$this->event->setValue("test", "1,2,3");
	 	$this->assertEquals($this->event->getValue("test"), "1,2,3");
	 }
}
