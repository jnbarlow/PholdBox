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
		$checksum1 = "";
		$checksum2 = "";
		$hashedChecksum1 = hash("sha1", $_SERVER["REMOTE_ADDR"]);
		$hashedChecksum2 = hash("sha1", $_SERVER["HTTP_USER_AGENT"]);
		
		if(isset($_COOKIE["PHPSESSID"]))
		{
			$sessionId = $_COOKIE["PHPSESSID"];	
		}
		
		if(isset($_COOKIE["CHECKSUM1"]))
		{
			$checksum1 = $_COOKIE["CHECKSUM1"];	
		}
		
		if(isset($_COOKIE["CHECKSUM2"]))
		{
			$checksum2 = $_COOKIE["CHECKSUM2"];	
		}
		$this->setSessionId($sessionId);
		
		if($checksum1 != $hashedChecksum1 || $checksum2 != $hashedChecksum2)
		{
			$this->setSessionId(uniqid("", true));
			setcookie("PHPSESSID", $sessionId);
			setcookie("CHECKSUM1", $hashedChecksum1);
			setcookie("CHECKSUM2", $hashedChecksum2);
		}
		else
		{			
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
		$returnVal = "";
		
		if(isset($this->session[$key]))
		{
			$returnVal = $this->session[$key];
		}
		
		return $returnVal;	
	}
}
