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
	protected $IOC = array("MyObj", "test.MyObj");
	
	public function home()
	{
		$this->setView("home");
		
		$this->rc["MyObj"] = $this->instance["MyObj"]->getText();
		$this->rc["MyObj2"] = $this->instance["test\MyObj"]->getText();
		$this->renderView();
	}
	
	public function set()
	{
		$this->instance["MyObj"]->setId($this->getValue("id"));
		$this->instance["MyObj"]->setName($this->getValue("name"));
		$this->instance["MyObj"]->setTitle($this->getValue("title"));
		print($this->instance["MyObj"]->save() . " row(s) affected.");
	}
	
	public function get()
	{
		$this->instance["MyObj"]->setId($this->getValue("id"));
		$this->instance["MyObj"]->setName($this->getValue("name"));
		$this->instance["MyObj"]->setTitle($this->getValue("title"));
		$this->instance["MyObj"]->load();
		
		print($this->instance["MyObj"]->getId() . "<br>");
		print($this->instance["MyObj"]->getName() . "<br>");
		print($this->instance["MyObj"]->getTitle() . "<br>");
		
	}
	
	public function update()
	{
		$this->instance["MyObj"]->setId($this->getValue("id"));
		$this->instance["MyObj"]->load();
		
		print("Before: <br>");
		print($this->instance["MyObj"]->getId() . "<br>");
		print($this->instance["MyObj"]->getName() . "<br>");
		print($this->instance["MyObj"]->getTitle() . "<br>");
		print("---------------<br>");
		
		if($this->getValue("name") != '')
		{
			$this->instance["MyObj"]->setName($this->getValue("name"));
		}
		if($this->getValue("title") != '')
		{
			$this->instance["MyObj"]->setName($this->getValue("title"));
		}
		print($this->instance["MyObj"]->save() . " row(s) affected.<br>");
		print("---------------<br>");
		print("After: <br>");
		print($this->instance["MyObj"]->getId() . "<br>");
		print($this->instance["MyObj"]->getName() . "<br>");
		print($this->instance["MyObj"]->getTitle() . "<br>");
		
	}
	
	public function bulkSave()
	{
		$objs = array(new MyObj(), new MyObj(), new MyObj());
		
		$objs[0]->setName("bstest1");
		$objs[0]->setTitle("bsTitle1");
		
		$objs[1]->setName("bstest2");
		$objs[1]->setTitle("bsTitle2");
		
		$objs[2]->setName("bstest3");
		$objs[2]->setTitle("bsTitle3");
		//print($objs[0]->bulkSave($objs));
	}
	
}
?>
