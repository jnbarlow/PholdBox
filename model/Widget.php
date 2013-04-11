<?php
//namespace myapp;
/*
 * Created on Nov 3, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class Widget extends \system\PhORM //<- uncomment to enable ORM
{
	protected $ORM = array("tableName"=>"widget",
						   "dsn"=>"",
						   "columns"=>array("id", "myobjid", "name"),
						   "types"=>array("int(1)", "int(1)", "varchar(25)"),
						   "values"=>array());
						   
	public function getText()
	{
		return "In MyObj";
	}
}
?>
