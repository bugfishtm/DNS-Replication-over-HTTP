<?php
	/*
		__________              _____.__       .__     
		\______   \__ __  _____/ ____\__| _____|  |__  
		 |    |  _/  |  \/ ___\   __\|  |/  ___/  |  \ 
		 |    |   \  |  / /_/  >  |  |  |\___ \|   Y  \
		 |______  /____/\___  /|__|  |__/____  >___|  /
				\/     /_____/               \/     \/  MRoD Domain Functions File */
	// True of False if Domain Exists
	function dnshttp_server_id_exists($mysql, $id) { 
		if(is_numeric($id)) { 
			$query = mysqli_query($mysql->mysqlcon, "SELECT * FROM "._TABLE_SERVER_." WHERE id = $id");
			while ($result	=	mysqli_fetch_array($query) ) { return true; } 
		} return false; 
	}	
	
 	function dnshttp_server_check($host, $port) {$f = @fsockopen($host, $port, $errno, $errstr, 1);if ($f !== false) {$res = fread($f, 1024) ;if (strlen($res) > 0 && strpos($res,'220') === 0){@fclose($f);return true;}else{@fclose($f);return false;}} return false;}
	
	function dnshttp_server_name_exists($mysql, $domain_name) { 
		if(trim($domain_name) != "") { 
			$query = mysqli_query($mysql->mysqlcon, "SELECT * FROM "._TABLE_USERS_." WHERE id = \"".$mysql->escape($domain_name)."\"");
		while ($result	=	mysqli_fetch_array($query) ) { return $result["user"]; } 
		
		} return false; 
	}	
	
	// Get all Informations of a Domain
	function dnshttp_server_get($mysql, $id) {
		if(is_numeric($id)) { 
			$query = mysqli_query($mysql->mysqlcon, "SELECT * FROM "._TABLE_SERVER_." WHERE id = \"".$mysql->escape($id)."\"");
			while ($result	=	mysqli_fetch_array($query) ) { return $result; } 
		} return false; 		
	}
	
	// Register a New Domain
	function dnshttp_server_delete_by_id($mysql, $id) {
		// If Exists, Do not Register
		if(dnshttp_server_id_exists($mysql, $id)) {  
			return $mysql->query("DELETE FROM "._TABLE_SERVER_." WHERE id = '$id';");
		} return false;		
	}
	
	// Register a New Domain
	function dnshttp_server_delete_by_name($mysql, $domain) {
		// If Exists, Do not Register
		if(dnshttp_server_name_exists($mysql, $domain)) {  
			return $mysql->query("DELETE FROM "._TABLE_SERVER_." WHERE domain = '".$mysql->escape(trim($domain))."';");
		} return false;		
	}
?>