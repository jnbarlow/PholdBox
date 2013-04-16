<?php
namespace test;
/*
 * Created on Nov 3, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class MyObj extends \system\Model
{
	public function getText()
	{
		$sessionVal = $this->getSessionValue("test");
		$this->debug($this, "Debugger from Obj extending abstract Model");
		$this->setSessionValue("test", "Test Value");
		return "In MyObj (test.MyObj) -- Session Val:" . $sessionVal;	
	}
}
