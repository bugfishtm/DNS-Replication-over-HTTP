<?php
	/*	__________ ____ ___  ___________________.___  _________ ___ ___  
		\______   \    |   \/  _____/\_   _____/|   |/   _____//   |   \ 
		 |    |  _/    |   /   \  ___ |    __)  |   |\_____  \/    ~    \
		 |    |   \    |  /\    \_\  \|     \   |   |/        \    Y    /
		 |______  /______/  \______  /\___  /   |___/_______  /\___|_  / 
				\/                 \/     \/                \/       \/  IP Blacklisting Control Class */	
	
	class x_class_ipblacklist {
		// Class Variables
		private $mysqlcon   	= false; 
		private $table     	= false; 
		private $ip     		= false; 
		private $max     	= false; 
		public $blocked  	= false; 
		
		// Construct
		function __construct($mysql, $tablename, $maxvalue = 50000) { 
			$this->mysqlcon  = $mysql; 
			$this->table     = $tablename; 
			$this->ip  	  	 = @$_SERVER["REMOTE_ADDR"]; 
			$this->max  	 = $maxvalue; 
			$this->blocked   = $this->isblocked(); 
		}
			
		public function isblocked() {
			$rres = @mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table." WHERE ip_adr = '".$this->ip."' AND failures > ".$this->max.";"); 
			while ($sresult = @mysqli_fetch_array($rres, MYSQLI_BOTH)){	return true; }
			return false;		
		}
		
		public function counter($ip = false) {
			if(!$ip) { $rres = @mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table." WHERE ip_adr = '".$this->ip."' AND failures > ".$this->max.";"); }
			else { $rres = @mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table." WHERE ip_adr = '".$ip."' AND failures > ".$this->max.";"); }
			while ($sresult = @mysqli_fetch_array($rres, MYSQLI_BOTH)){	return $sresult["failure"]; }
			return 0;		
		}
		
		public function raise() {
			$rres = @mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table." WHERE ip_adr = '".$this->ip."';"); 
			while ($sresult = @mysqli_fetch_array($rres, MYSQLI_BOTH)){	
				return @mysqli_query($this->mysqlcon, "UPDATE ".$this->table." SET failures = failures + 1 WHERE id = '".$sresult["id"]."';"); 
			}
			@mysqli_query($this->mysqlcon, "INSERT INTO ".$this->table."(ip_adr, failures) VALUES('".mysqli_real_escape_string($this->mysqlcon, @$this->ip)."', 1);"); 				
		}	
	}
?>