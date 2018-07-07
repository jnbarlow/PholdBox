<?php

class PholdBoxSesionManagerTest extends PholdBoxTestBase
{
    protected function setUp()
    {
		parent::setUp();
		$SYSTEM = array();
		$GLOBALS['SYSTEM'] = &$SYSTEM;
        $GLOBALS['rc'] = null;
        $_SERVER["REMOTE_ADDR"] = 'foo';
        $_SERVER["HTTP_USER_AGENT"] = 'bar';
        $_COOKIE[] = [];
    }

    protected function tearDown()
    {
        $_SERVER["REMOTE_ADDR"] = null;
        $_SERVER["HTTP_USER_AGENT"] = null;
        $_COOKIE[] = [];
    }
    
    /**
     * covers pushToSession and getFromSession
     *
     * @return void
     */
    public function testPushToSession()
    {
        $session = base64_encode(json_encode(array('foo'=>'bar')));

        $mock = $this->getMockBuilder('system\PholdBoxSessionManager')
        ->disableOriginalConstructor()
        ->setMethods(['save', 'load'])
        ->getMock();

        $mock->expects($this->once())
        ->method('save');

        $mock->pushToSession('foo', 'bar');
        $value = $mock->getFromSession('foo');
        $this->assertEquals('bar', $value);
        $this->assertEquals($session, $mock->getSession());
    }

    //public function testPushToSession()
}