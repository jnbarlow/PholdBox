<?php
namespace system;
/*
 * Created on Nov 3, 2010
 * John Barlow
 * 
 * Event
 * 	This is the general event hanlding portion of PholdBox. Calls
 * 	to ?event=<event> get processed by this file, and all handlers need
 * 	to extend this object.
 */
class Event extends PholdBoxBaseObj
{
	protected $layout;
	protected $rc;
	protected $view;
	protected $evtObj;
	protected $useLayout;
	protected $IOC;
	
	public function __construct($layout)
	{
		parent::__construct();
		$this->layout = $layout;
		$this->useLayout = true;
	}

	/* 
		Name: setView
		
		Does: This function sets the view that is rendered by this event.
			The option of $useLayout can be used to bipass loading the view
			inside of a layout.  This is useful for AJAX calls.
	*/
	public function setView($view, $useLayout = true)
	{	
		$this->view = $view;
		$this->useLayout = $useLayout;
	}
	
	/*
		Name: setLayout
		
		Does: Allows you to change the layout of this event dynamically
	*/
	public function setLayout($layout)
	{	
		$this->layout = $layout;
	}	
	
	/*
		Name: renderView
		
		Does: Called from handlers to render the view to the page.
	*/
	public function renderView()
	{
		//Adds current RC scope back to globals, so that the view can access it at $rc
		$GLOBALS["rc"] = $this->rc;
		if($this->useLayout)
		{
			$this->renderLayout("views/" . $this->view . ".php");
		}
		else
		{
			include("views/" . $this->view . ".php");
		}
	}
	
	/*
		Name: processEvent
		
		Does: This function is called by the system to process the ?event=<event>
			in the URL.  This creates the event object specified and calls the
			appropriate function in the object.  The dotResolver is utilized here.
	*/	
	public function processEvent($event)
	{
		$resolved = $this->dotResolver($event);
		$handler = "handlers/" . $resolved->pathArray[0] . ".php";
		$pathArray = $resolved->pathArray;
		
		if(file_exists($handler))
		{
			include($handler);
		}
		else
		{
			print("Invalid Handler: $event");
			exit;
		}
		
		$this->evtObj = new $resolved->evtClass($this->layout);
		$this->evtObj->preEvent();
		$this->evtObj->$pathArray[1]();
		
	}
	
	/*
		Name: renderLayout
		
		Does: Called by the System to render the defined layout.
	*/
	private function renderLayout($view)
	{
		$rc = $this->rc;
									
		include("layouts/" . $this->layout . ".php");
	}
	
	/*
	 * Name: getValue
	 * Does: safely returns a value from the request collection
	 * 
	 */
	public function getValue($value)
	{
		$rc = $this->rc;
		if(array_key_exists($value, $rc))
		{
			return $rc[$value];
		}
		return "";
	}
	
	/*
	 * Name: setValue
	 * Does: sets a value into the request collection
	 */
	 public function setValue($key, $value)
	 {
	 	$this->rc[$key] = $value;
	 }
	 
	 /**
	  * runEvent
	  * Runs an event
	  * @parm string event Event to run in dot notation
	  */
	  public function runEvent($event)
	  {
	  		header("Location: ?event=" . $event);
	  }
	  
	  /**
	   * preEvent
	   * event that gets ran from subclasses before other events in that class
	   */
	   public function preEvent()
	   {
	   		//this intentionally blank, must be set up in the child class to use.
	   }
}
?>
