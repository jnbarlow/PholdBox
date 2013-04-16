<?php
namespace system;
/*
 * Created on Nov 3, 2010
 * John Barlow
 * 
 * PholdBoxBaseObj
 * 	This is where most of the Magic happens.  Contains IOC and Dot Resolver
 *	functions used by the rest of PholdBox. Everything PholdBox related needs
 * 	to come from this Object.
 */

class PholdBoxBaseObj
{
	/**
	 * @property array instance This is the array in which all IOC objects are stored 
	 */
	protected $instance = array();
	
	/**
	 * @property array SYSTEM Static array of system variables
	 */
	static protected $SYSTEM = array();
	
	/**
	 * @property array IOC Array of text fields describing the objects you wish to inject
	 */
	protected $IOC;
	
	/**
	 * @property array rc This is the Request collection. This object is exposed to the view layer, so that any data rollup can happen
	 * here.  All post/get variables are shoved into this as well, following the rule that POST variables win over GET.
	 */
	protected $rc;	
	
	/**
		Constructor:  Child classes need to call this through parent::__construct() to 
		get IOC elemets processed;
	*/
	public function __construct()
	{
		$this->SYSTEM = &$GLOBALS["SYSTEM"];
		$this->rc = $GLOBALS["rc"];
		$this->processIOC();
	}
	
	/**
		Name: dotResolver
		
		Does: This is the dot resolver that is used to convert handler and model 
			calls in "obj.function" format to actually loading the objects.  Nested 
			folders are also handled in "folder.folder.folderN.obj.fcn"
			This function also requires php 5.3 for the use of packages.
		
		Returns: Structure containing exploded paths, object names, package names, 
			and functions for use in handlers and model objects.  Both use this information
			slightly differently, and thus this function has a couple logic paths it follows
			to build the structure.
		
		stReturn: 
			evtClass - class if event object
			modelClass - class if model object
			modelPath - physical location of model file from webroot
			pathArray - passed in "dot" path exploded into an array (useful for debugging)
		
		Note: Model objects directly in the model folder are assumed to have no namespace.
			Assigning them a package will make php fail to load them.
			  
	*/
	protected function dotResolver($path)
	{
		$stReturn = new \stdClass();
		$evtClass = "";
		
		$dotCount = substr_count($path, ".");
		$pathArray = explode(".", $path);
		if($dotCount > 0)
		{
			$evtClass = $pathArray[$dotCount-1];
		}
		$modelClass = $pathArray[$dotCount];
		
		if($dotCount > 0)
		{
			$path = preg_replace("/\./", "/", $path, $dotCount - 1);
		}
		$pathArray = explode(".", $path);

		$stReturn->evtClass = $evtClass;
		$stReturn->modelClass = $modelClass;
		$modelNamespace = "";
		
		if($dotCount > 0)
		{
			$stReturn->modelPath = "model/" . $pathArray[0] . "/" . $modelClass . ".php";
			$modelNamespace = str_replace("/", "\\", $pathArray[0]) . "\\";
			$stReturn->modelClass = $modelNamespace . $stReturn->modelClass;
		}
		else
		{
			$stReturn->modelPath = "model/" . $modelClass . ".php";
		}	
		$stReturn->pathArray = $pathArray;
		
		return $stReturn;
	}
	
	/**
		Name: processIOC
		
		Does: This function allows for Spring style object injection.  It works
			in Conjuction with the dotResolver above to determine the physical location of
			a Class so that an Object of that type can be instantiated.  All IOC created 
			objects get placed into the local $instance array.
			
			This function works by parsing a local $IOC array inside of the child class.
			Objects you want to be injected need to be listed in dot notation inside the array.
			
			Example:
			protected $IOC = array("MyObj", "test.MyObj");
			
			This would load MyObj from the model folder, then load MyObj from the model/test/ 
			folder.  Namespace use is important here, as the MyObj in model/test/ MUST be in the
			"test" namespace, otherwise (beside from the dotResolver not finding it) PHP would overwrite
			the first MyObj with the second MyObj. 
			
			The created objects go into the $instance array based on their namespace.  For example, the 
			$insance array created from the statement above would have two keys: "MyObj" and "test\MyObj"
			
			Why "test\MyObj" you ask?  Well, that's how you refer to it in namespace notation, so for the
			sake of keeping things simple, this is how the keys are set up.
	*/
	protected function processIOC()
	{
		if(is_array($this->IOC))
		{ 
			foreach($this->IOC as $object)
			{
				$resolved = $this->dotResolver($object);
				$model = $resolved->modelPath;
				
				if(file_exists($model))
				{
					include_once($model);
				}
				else
				{
					print("Invalid Model: $object");
					exit;
				}
				
				//capture debug timing
				if((isset($this->SYSTEM["debug"]) && $this->SYSTEM["debug"]))
	   			{
	   				$startTime = microtime();
	   			}
				
				$this->instance[$resolved->modelClass] = new $resolved->modelClass;
				
				//capture debug output
				if((isset($this->SYSTEM["debug"]) && $this->SYSTEM["debug"]))
	   			{	
					$this->pushDebugStack($this->instance[$resolved->modelClass], "Model", microtime() - $startTime);
	   			}
			}
		}
	} 
	
	//TODO: implement for chaining?
	public function __call($name, $arguments)
 	{
 		print("Call fired: ". $name . "<br>");
 	}
 	
	/**
	* varDumpToString
	* private util function to capture the output of var_dump
	*/
	protected function varDumpToString ($var)
	{
		ob_start();
		var_dump($var);
		$result = ob_get_clean();
		return $result;
	}
	
	/**
	 * pushDebugStack
	 * Pushes an object to the debug stack trace.
	 * @param mixed $obj Object being pushed to the stack
	 * @param string $type
	 * @param mixed $time Timing info - or ""
	 */
	protected function pushDebugStack ($obj, $type, $time)
	{
		if($type == "Function")
		{
			$this->SYSTEM["debugger"]["stack"][] = array("name" => $obj, "object" => null, "type" => $type, "timing" => $time);
		}
		else
		{
			$this->SYSTEM["debugger"]["stack"][] = array("name" => get_class($obj), "object" => $obj, "type" => $type, "timing" => $time);	
		}
		
	}
	
	/**
	 * debug
	 * Allows user to push strings/objects to the user debug stack to be dumped
	 * @param mixed $obj Object to dump
	 * @param string $label Label for your object
	 */
	 public function debug($obj, $label = "unnamed")
	 {
	 	$this->SYSTEM["debugger"]["userStack"][] = array("name" => $label, "object" => $obj);
	 }
	 
	 /**
	  * setSessionValue
	  * Sets a value to the sesion objet
	  * @param string $key Key in array to set
	  * @param mixed $value Value to set
	  */
	 public function setSessionValue($key, $value)
	 {
	 	$GLOBALS["SESSION"]->pushToSession($key, $value);	
	 }
	 
	 /**
	  * getSessoinValue
	  * Gets a value from the session object
	  * @param string $key Key to retrieve from the session
	  * @return mixed Value of Key
	  */
	 public function getSessionValue($key)
	 {
	 	return $GLOBALS["SESSION"]->getFromSession($key);
	 }
}
