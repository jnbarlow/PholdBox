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
		$this->debug($this, "Debugger from Obj extending abstract Model");
		return "In MyObj (test.MyObj)";	
	}
}
?>
