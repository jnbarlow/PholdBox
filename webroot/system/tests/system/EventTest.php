<?php

class EventTest extends PholdBoxTestBase
{
	protected function setUp(){
		parent::setUp();
		$SYSTEM = array();
		$GLOBALS['SYSTEM'] = &$SYSTEM;
		$GLOBALS['rc'] = null;
    }
    
    public function testGetSetValue()
    {
        $mock = $this->getMockBuilder('system\Event')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $mock->setValue('foo', 'bar');
        $this->assertEquals('bar', $mock->getValue('foo'));
        $this->assertEquals('', $mock->getValue('undefined'));
    }

    public function testProcessEvent()
    {
        $resolved = new \stdClass();
        $resolved->evtClass = '';
        $resolved->pathArray = array('foo', 'preEvent');
        
        $mock = $this->getMockBuilder('system\Event')
            ->disableOriginalConstructor()
            ->setMethods(array('loadResource', 'getClassName', 'dotResolver', 'pushDebugStack', 'preEvent'))
            ->getMock();
        
        $mock->expects($this->once())
			->method('loadResource')
			->will($this->returnValue(true));
		
		$mock->expects($this->once())
			->method('dotResolver')
            ->will($this->returnValue($resolved));
        
        $mock->expects($this->once())
			->method('getClassName')
            ->will($this->returnValue('system\Event'));
        
        $mock->processEvent('foo');
    }
}