<?php
namespace system;
/*
 * Created on Nov 3, 2010
 * John Barlow
 * 
 * RCProcessor
 *
 *	This class defines the RC scope utilized by the application.  URL and Form arrays are consolidated 
 * 	into the RC collection.  In case of collision, Form variables win ties.
 */
class RCProcessor
{
	/**
	 * @property array rc This is the Request collection. This object is exposed to the view layer, so that any data rollup can happen
	 * here.  All post/get variables are shoved into this as well, following the rule that POST variables win over GET.
	 */
	protected $rc = array();
	
	/* loop through URL and Form arrays to consolidate all variables into the RC collection
	   Form variables win ties
	*/
	public function __construct()
	{
		while(list($key, $val) = each($_GET))
		{
			$this->rc[$key] = $val;
		}
		
		while(list($key, $val) = each($_POST))
		{
			$this->rc[$key] = $val;
		}
	}

	public function getCollection()
	{
		return $this->rc;
	}
}
?>
