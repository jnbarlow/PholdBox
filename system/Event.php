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
	/**
	 * @property string layout The current layout that is being used
	 */
	protected $layout;
		
	/**
	 * @property string view The current view that is being used
	 */
	protected $view;
	
	/**
	 * @property object evtObj The dynamically created object based on the event definition in the url
	 */
	protected $evtObj;
	
	/**
	 * @property boolean useLayout Bool to tell the system to render the view with or without a layout, used in setView()
	 */
	protected $useLayout;
		
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
	protected function renderView()
	{
		$rc = $this->rc;
		if($this->useLayout)
		{
			$this->renderLayout("views/" . $this->view . ".php");
		}
		else
		{
			$this->SYSTEM["debugger"]["showDebugger"] = false;
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
			include_once($handler);
		}
		else
		{
			print("Invalid Handler: $event");
			return -1;
		}
		
		//capture debug timing
		if((isset($this->SYSTEM["debug"]) && $this->SYSTEM["debug"]))
		{
			$startTime = microtime();
		}
		$this->evtObj = new $resolved->evtClass($this->layout);
		
		//capture debug output
		if((isset($this->SYSTEM["debug"]) && $this->SYSTEM["debug"]))
		{
			$this->pushDebugStack($this->evtObj, "Event", microtime() - $startTime);
		}
		
		//capture debug timing
		if((isset($this->SYSTEM["debug"]) && $this->SYSTEM["debug"]))
		{
			$startTime = microtime();
		}
		$this->evtObj->preEvent();
		
		//capture debug output
		if((isset($this->SYSTEM["debug"]) && $this->SYSTEM["debug"]))
		{
			$this->pushDebugStack($resolved->evtClass . ".preEvent()", "Function", microtime() - $startTime);
		}
		
		//capture debug timing
		if((isset($this->SYSTEM["debug"]) && $this->SYSTEM["debug"]))
		{
			$startTime = microtime();
			$this->pushDebugStack($resolved->evtClass . "." . $pathArray[1] . "() Start", "Function", "");
		}
		$this->evtObj->$pathArray[1]();
		
		//capture debug output
		if((isset($this->SYSTEM["debug"]) && $this->SYSTEM["debug"]))
		{
			$this->pushDebugStack($resolved->evtClass . "." . $pathArray[1] . "() End", "Function", microtime() - $startTime);
		}		
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
	   
	  /**
	   * renderDebugger
	   * Renders the debug output for this event. -- self contained HTML in this function.
	   */
	   public function renderDebugger()
	   {
	   		$startTime = microtime();
	   		if((isset($this->SYSTEM["debug"]) && $this->SYSTEM["debug"]) && $this->useLayout)
	   		{
	   			$html  = "<script>";
	   			$html .= 	"function toggleDebugPanel(node){";
	   			$html .= 		"var domNode = document.getElementById(node);";
	   			$html .=    	"if(domNode.style.display == 'block'){";
	   			$html .=			"domNode.style.display = 'none';";
	   			$html .=		"}";
	   			$html .=		"else{";
	   			$html .=			"domNode.style.display = 'block'";
	   			$html .=		"}";
	   			$html .=	"}";
	   			$html .= "</script>";
	   			$html .= "<div style='clear:both;background-color:#dddddd;padding:5px;margin-top:10px'>";
	   			$html .=	"<h3>PholdBox Debugger</h3>";
	   			$html .=	"<p>PholdBox Version: " . $this->rc["PB_VERSION"] . "<br>";
	   			$html .= 	"Template Rendering Time: " . number_format(($this->SYSTEM["debugger"]["endTime"] - $this->SYSTEM["debugger"]["startTime"]), 4) . "s<br>";  
	   			$html .=    "Total Memory Usage: " . number_format(memory_get_usage(true)/1024/1024, 2) . "M<p>";
	   			$html .= 	"<div style='background-color:#eeeeee;margin:0px 5px;padding:5px;'>";
	   			$html .=		"<span style='cursor:pointer' onclick='toggleDebugPanel(\"debugRC\")'>Request Collection</span><br>";
	   			$html .=		"<pre id='debugRC' style='background-color:white;display:none'>" . $this->varDumpToString($this->rc) . "</pre>";	   		
	   			$html .=	"</div>";
	   			
	   			$html .=	"<h3>User Debug Trace</h3>";
	   			$html .=	"<div>";
	   			$counter = 0;
	   			foreach($this->SYSTEM["debugger"]["userStack"] as $item)
	   			{	
		   			$html .= 	"<div style='background-color:#eeeeee;margin:2px 5px;padding:5px;'>";
		   			$html .=		"<span style='cursor:pointer;clear:both' onclick='toggleDebugPanel(\"userDebug" . $counter . "\")'>" . $item["name"] . "</span><br>";
		   			$html .=		"<pre id='userDebug" . $counter . "' style='background-color:white;display:none'>" . $this->varDumpToString($item["object"]) . "</pre>";	   		
		   			$html .=	"</div>";
		   				   			
		   			$counter++;
	   			}
	   			$html .=	"</div>";
	   			
	   			$html .=	"<h3>Stack Trace</h3>";
	   			$html .=	"<div>";
	   			$counter = 0;
	   			$nesting = 1;
	   			foreach($this->SYSTEM["debugger"]["stack"] as $item)
	   			{
	   				if(preg_match("/\(\) End/", $item["name"])){
		   				$nesting--;
		   			}
					if($nesting > 1){
						$tickerColor = "#999999";
					}		   		
					else
					{
						$tickerColor = "#000000";
					}
					$timing = "";
					
					if($item["timing"] != ""){
						$timing = number_format($item["timing"], 4) . "s";
					}
		   			$html .= 	"<div style='background-color:#eeeeee;margin:2px 5px 2px " . $nesting * 10 . "px;padding:5px;'>";
		   			$html .=		"<span style='cursor:pointer;clear:both' onclick='toggleDebugPanel(\"debug" . $counter . "\")'>" . $item["type"] . ": " . $item["name"] . "</span><span style='float:right;color:" . $tickerColor . "'>" . $timing . "</span><br>";
		   			$html .=		"<pre id='debug" . $counter . "' style='background-color:white;display:none'>" . $this->varDumpToString($item["object"]) . "</pre>";	   		
		   			$html .=	"</div>";
		   			if(preg_match("/\(\) Start/", $item["name"])){
		   				$nesting++;
		   			}
		   			
		   			$counter++;
	   			}
	   			$html .=	"</div>";
	   			$html .= "<h3>Debugger Rendering Time: " . number_format(microtime() - $startTime, 4) . "s";
	   			$html .= "</div>";
	   			echo $html;
	   		}
	   }
}
?>
