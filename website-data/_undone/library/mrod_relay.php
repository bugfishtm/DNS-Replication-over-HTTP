<?php
	/*
		__________              _____.__       .__     
		\______   \__ __  _____/ ____\__| _____|  |__  
		 |    |  _/  |  \/ ___\   __\|  |/  ___/  |  \ 
		 |    |   \  |  / /_/  >  |  |  |\___ \|   Y  \
		 |______  /____/\___  /|__|  |__/____  >___|  /
				\/     /_____/               \/     \/  MRoD Domain Functions File */
	// True of False if Domain Exists
	function mrod_relay_id_exists($mysql, $id) { 
		if(is_numeric($id)) { 
			$query = mysqli_query($mysql->mysqlcon, "SELECT * FROM "._TABLE_RELAY_." WHERE id = \"".$mysql->escape($id)."\"");
			while ($result	=	mysqli_fetch_array($query) ) { return true; } 
		} return false; 
	}	
	
 	function mrod_relay_check($host, $port) {$f = @fsockopen($host, $port, $errno, $errstr, 1);if ($f !== false) {$res = fread($f, 1024) ;if (strlen($res) > 0 && strpos($res,'220') === 0){@fclose($f);return true;}else{@fclose($f);return false;}} return false;}
	
	function mrod_relay_name_exists($mysql, $domain_name) { 
		if(trim($domain_name) != "") { 
			$query = mysqli_query($mysql->mysqlcon, "SELECT * FROM "._TABLE_USERS_." WHERE id = \"".$mysql->escape($domain_name)."\"");
		while ($result	=	mysqli_fetch_array($query) ) { return $result["user"]; } 
		
		} return false; 
	}	
	
	// Get all Informations of a Domain
	function mrod_relay_get($mysql, $id) {
		if(is_numeric($id)) { 
			$query = mysqli_query($mysql->mysqlcon, "SELECT * FROM "._TABLE_RELAY_." WHERE id = \"".$mysql->escape($id)."\"");
			while ($result	=	mysqli_fetch_array($query) ) { return $result; } 
		} return false; 		
	}
	
	// Register a New Domain
	function mrod_relay_register($mysql, $domain, $type, $fk_relay, $fk_user, $ovr_servername = false, $ovr_serverport = false, $ovr_smtps = false) {
		// If Exists, Do not Register
		if(mrod_relay_name_exists($mysql, $domain)) { return false; }
		
		// Prepare Query Arrays
		$querys = array();
		
		// Prepare Variables
		if(is_string($domain)) 		 { $domain = trim($domain); } else { return false; }
		if(!is_string($type)) 	 	 { return false; }
		if(!kw_relay_id_exists($mysql, $fk_relay)) 		 { $fk_relay = "NULL"; }
		if(!is_numeric($fk_user)) 		 { $fk_user = "NULL"; }
		if(!$ovr_servername) { } else {} 
		if(!$ovr_serverport) { } else {}
		if(!$ovr_smtps) 	 { } else {}
		
		$mysql->query("INSERT INTO "._TABLE_RELAY_."(domain, type, fk_relay, fk_user) VALUES('".$mysql->escape($domain)."', '".$mysql->escape($domain)."', ".$fk_relay.", ".$fk_user.");");
		
		if(is_numeric($id)) { 
			$query = mysqli_query($mysql, "SELECT * FROM "._TABLE_RELAY_." WHERE id = \"".$mysql->escape($id)."\"");
			while ($result	=	mysqli_fetch_array($query) ) { return $result; } 
		} return false; 		
	}
	
	// Register a New Domain
	function mrod_relay_delete_by_id($mysql, $id) {
		// If Exists, Do not Register
		if(mrod_relay_id_exists($mysql, $id)) {  
			return $mysql->query("DELETE FROM "._TABLE_RELAY_." WHERE id = '$id';");
		} return false;		
	}
	
	// Register a New Domain
	function mrod_relay_delete_by_name($mysql, $domain) {
		// If Exists, Do not Register
		if(mrod_relay_name_exists($mysql, $domain)) {  
			return $mysql->query("DELETE FROM "._TABLE_RELAY_." WHERE domain = '".$mysql->escape(trim($domain))."';");
		} return false;		
	}
?>