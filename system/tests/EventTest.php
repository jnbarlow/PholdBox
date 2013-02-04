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
	
	/**
	 * @runInSeparateProcess
	 */
	function testProcessEvent(){
		$this->event = new system\Event("layout.main");
		$this->event->processEvent("main.home");
		$output = $this->getActualOutput();
		$this->assertContains("Welcome", $output);
	}
	
	/**
	 * @runInSeparateProcess
	 */
	function testInvalidHandler(){
		$this->event = new system\Event("layout.main");
		$this->event->processEvent("foo.bar");
		$this->expectOutputString("Invalid Handler: foo.bar");
	}
	
	/**
	 * @runInSeparateProcess
	 */
	function testMissingHandlerFunction(){
		$this->event = new system\Event("layout.main");
		$this->event->processEvent("main.bar");
		$this->expectOutputString("Call fired: bar<br>");
	}
	
	/**
	 * @runInSeparateProcess
	 */
	function testDebugOutput(){
		$SYSTEM["debug"] = true;
		$this->event = new system\Event("layout.main");
		$this->event->renderDebugger();
		$output = $this->getActualOutput();
		$this->assertContains("Debugger", $output);
	}
	
	/**
	 * @runInSeparateProcess
	 */
	function testSetLayout(){
		$this->event = new system\Event("layout.main");
		$this->event->setLayout("layout.main");
		$this->event->processEvent("main.home");
		$output = $this->getActualOutput();
		$this->assertContains("Welcome", $output);
	}
	
	/**
	 * @runInSeparateProcess
	 * @expectedException PHPUnit_Framework_Error
	 */
	function testSetLayout_invalidFile(){
		$this->event = new system\Event("layout.main");
		$this->event->setLayout("layout.bleh");
		$this->event->processEvent("main.home");
	}
	
	/**
	 * @runInSeparateProcess
	 */
	 function testSetValue_GetValue(){
	 	$this->event = new system\Event("layout.main");
	 	$this->event->setValue("test", "1,2,3");
	 	$this->assertEquals($this->event->getValue("test"), "1,2,3");
	 }
}
?>
