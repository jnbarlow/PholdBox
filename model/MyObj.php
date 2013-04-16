<?php
//namespace myapp;
/*
 * Created on Nov 3, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class MyObj extends \system\PhORM //<- uncomment to enable ORM
{
	protected $ORM = array("tableName"=>"test",
						   "dsn"=>"",
						   "columns"=>array("id", "name", "title"),
						   "types"=>array("int(1)", "varchar(25)", "varchar(25)"),
						   "values"=>array());
	
	public function getText()
	{
		return "In MyObj";
	}
}
