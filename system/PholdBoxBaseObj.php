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
	protected $instance = array();
	protected $SYSTEM = array();
	protected $IOC;
	/*
		Constructor:  Child classes need to call this through parent::__construct() to 
		get IOC elemets processed;
	*/
	public function __construct()
	{
		$this->processIOC();
	}
	
	/*
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
		$stReturn = "";
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
	
	/*
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
				$this->instance[$resolved->modelClass] = new $resolved->modelClass;	
			}
		}
	} 
	
	//TODO: implement for chaining?
	public function __call($name, $arguments)
 	{
 		print("Call fired: ". $name . "<br>");
 	}
}
?>