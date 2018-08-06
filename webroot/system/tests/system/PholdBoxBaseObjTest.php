<?php

class PholdBoxBaseObjTest extends PholdBoxTestBase
{
	protected function setUp(){
		parent::setUp();
		$SYSTEM = array();
		$GLOBALS['SYSTEM'] = &$SYSTEM;
		$GLOBALS['rc'] = null;
	}

	protected function tearDown()
	{
		$GLOBALS['SYSTEM'] = null;
	}

	/**
	 * @dataProvider dotResolverProvider
	 *
	 * @return void
	 */
	public function testDotResolver($path, $vals)
	{
		$mock = $this->getMockBuilder('system\PholdBoxBaseObj')
			->disableOriginalConstructor()
			->setMethods(null)
			->getMock();

		$path = $this->callPrivateMethod($mock, 'dotResolver', array($path));
		$this->assertEquals($vals['evtClass'], $path->evtClass);
		$this->assertEquals($vals['modelClass'], $path->modelClass);
		$this->assertEquals($vals['modelPath'], $path->modelPath);
		$this->assertEquals($vals['viewPath'], $path->viewPath);
	}

	public function dotResolverProvider()
	{
		return array(
			array('main.home', array(
				'evtClass' => 'main',
				'modelClass' => 'main\\home',
				'modelPath' => 'model/main/home.php',
				'viewPath' => 'views/main/home.php'
			)),
			array('main.home.long.path', array(
				'evtClass' => 'long',
				'modelClass' => 'main\\home\\long\\path',
				'modelPath' => 'model/main/home/long/path.php',
				'viewPath' => 'views/main/home/long/path.php'
			)),
			array('main/home/long/path', array(
				'evtClass' => 'long',
				'modelClass' => 'main\\home\\long\\path',
				'modelPath' => 'model/main/home/long/path.php',
				'viewPath' => 'views/main/home/long/path.php'
			))
		);
	}

	/**
	 * @dataProvider pushDebugStackProvider
	 *
	 * @return void
	 */
	public function testPushDebugStack($obj, $type, $time, $result)
	{
		$mock = $this->getMockBuilder('system\PholdBoxBaseObj')
			//->disableOriginalConstructor()
			->setMethods(null)
			->getMock();

		$this->callPrivateMethod($mock, 'pushDebugStack', array($obj, $type, $time));
		$this->assertEquals($result, $GLOBALS['SYSTEM']['debugger']['stack'][0]);
	}

	public function pushDebugStackProvider()
	{
		$obj = new \stdClass();
		return array(
			array('foo', 'Function', 1, array(
				'name'=>'foo',
				'object'=>null,
				'type'=>'Function',
				'timing'=>1
			)),
			array($obj, 'Object', 1, array(
				'name'=>'stdClass',
				'object'=>$obj,
				'type'=>'Object',
				'timing'=>1
			)),
		);
	}

	public function testDebug()
	{
		$result = array(
			'name' => 'bar', 
			'object' => 'foo'
		);

		$mock = $this->getMockBuilder('system\PholdBoxBaseObj')
			->setMethods(null)
			->getMock();

		$mock->debug('foo', 'bar');
		$this->assertEquals($result, $GLOBALS['SYSTEM']['debugger']['userStack'][0]);
	}

	public function testSetSessionValue()
	{
		$sessionMock = $this->getMockBuilder('system\PholdBoxSessionManager')
		->disableOriginalConstructor()
		->setMethods(array())
		->getMock();

		$sessionMock->expects($this->any())
		->method('pushToSession')
		->with('foo', 'bar');

		$mock = $this->getMockBuilder('system\PholdBoxBaseObj')
			->setMethods(array('getSessionObject'))
			->getMock();
		
		$mock->expects($this->any())
		->method('getSessionObject')
		->will($this->returnValue($sessionMock));

		$mock->setSessionValue('foo', 'bar');
	}

	public function testGetSessionValue()
	{
		$sessionMock = $this->getMockBuilder('system\PholdBoxSessionManager')
		->disableOriginalConstructor()
		->setMethods(array())
		->getMock();

		$sessionMock->expects($this->any())
		->method('getFromSession')
		->with('foo');

		$mock = $this->getMockBuilder('system\PholdBoxBaseObj')
			->setMethods(array('getSessionObject'))
			->getMock();
		
		$mock->expects($this->any())
		->method('getSessionObject')
		->will($this->returnValue($sessionMock));

		$mock->getSessionValue('foo');
	}

	/**
	 * @dataProvider processIOCProvider
	 *
	 * @param [type] $IOCItem
	 * @param [type] $callCount
	 * @param [type] $loadResult
	 * @param [type] $expectedResult
	 * @return void
	 */
	public function testProcessIOC($IOC, $callCount, $loadResult, $expectException)
	{
		$resolved = new \stdClass();
		$resolved->modelPath = '';
		$resolved->modelDotClass = 'system.PholdBoxBaseObj';
		$resolved->modelClass = 'system\PholdBoxBaseObj';

		$mock = $this->getMockBuilder('system\PholdBoxBaseObj')
			->setMethods(array('loadResource', 'dotResolver'))
			->disableOriginalConstructor()
			->getMock();
		
		$mock->expects($this->exactly($callCount))
			->method('loadResource')
			->will($this->returnValue($loadResult));
		
		$mock->expects($this->exactly($callCount))
			->method('dotResolver')
			->will($this->returnValue($resolved));

		if($expectException){
			$this->setExpectedException(\Exception::class);
		}

		$mock->setIOC($IOC);
		$this->callPrivateMethod($mock, 'processIOC', array());
	}

	public function processIOCProvider()
	{
		return array(
			array(array('system.PholdBoxBaseObj'), 1, true, false),
			array(array('system.PholdBoxBaseObj'), 1, false, true),
			array(array(), 0, false, false)
		);
	}

	public function testGetSetIOC()
	{
		$testArray = array('foo');
		$mock = $this->getMockBuilder('system\PholdBoxBaseObj')
			->setMethods(null)
			->disableOriginalConstructor()
			->getMock();

		$mock->setIOC($testArray);
		$result = $mock->getIOC();
		$this->assertEquals($testArray, $result);
	}
}
