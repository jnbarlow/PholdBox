<?php
namespace system;
/*
 * Created on Nov 3, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class PholdBoxSessionManager extends \system\PhORM
{
	protected $ORM = array("tableName"=>"pholdbox",
						   "dsn"=>"",
						   "columns"=>array("id", "session", "sessionId", "dateModified"),
						   "types"=>array("varchar(255)", "mediumtext", "varchar(45)", "varchar(45)"),
						   "values"=>array());
	
	protected $session = array();
	
	public function loadSession()
	{
		$sessionId = "";
		if(isset($_COOKIE["PHPSESSID"]))
		{
			$sessionId = $_COOKIE["PHPSESSID"];	
		}
		else
		{
			$sessionId = uniqid("", true);
			setcookie("PHPSESSID", $sessionId);
		}
		$this->setSessionId($sessionId);
		try
		{
			$this->load();
		}
		catch(\Exception $e)
		{
			echo $e->getMessage() . "<br>";
			echo "Since this is coming from the session manager, you probably haven't yet set up PholdBox's system table.<br>";
			echo "Please execute the following SQL in your database and try again:<br><br>";
			echo "CREATE TABLE `pholdbox` (<br>
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,<br>
				  `session` mediumtext,<br>
				  `sessionId` varchar(45) DEFAULT NULL,<br>
				  `dateModified` varchar(45) DEFAULT NULL,<br>
				  PRIMARY KEY (`id`),<br>
				  KEY `session_idx` (`sessionId`,`dateModified`)<br>
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
			die();
		}
		$this->session = json_decode($this->getSession(), true);
	}
	
	public function pushToSession($key, $value)
	{
		$this->session[$key] = $value;
		$this->setSession(json_encode($this->session));
		$this->setDateModified(strtotime("now"));
		$this->save();
	}
	
	public function getFromSession($key)
	{
		return $this->session[$key];	
	}
}
?>
