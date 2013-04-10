<?php
/*
 * This is an example class to show how you use events.  You can look for the documentation inside the
 * Event class itself to see what is available and how to use it.  I'll work on getting proper documentation
 * out there at some point -- honest :)
 * 
 * To use the functions below home(), you'll need to enable a DSN, create the table outlined in
 * MyObj, and make MyObj extend PhORM
 * 
 */
class Main extends system\Event
{
	protected $IOC = array("test.MyObj");
	
	public function home()
	{
		$this->setView("home");
		$this->renderView();
	}
}
?>
