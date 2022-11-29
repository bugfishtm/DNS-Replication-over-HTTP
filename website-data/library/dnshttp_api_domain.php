<?php
	/*
		__________              _____.__       .__     
		\______   \__ __  _____/ ____\__| _____|  |__  
		 |    |  _/  |  \/ ___\   __\|  |/  ___/  |  \ 
		 |    |   \  |  / /_/  >  |  |  |\___ \|   Y  \
		 |______  /____/\___  /|__|  |__/____  >___|  /
				\/     /_____/               \/     \/  MRoD Domain Functions File */
	// True of False if Domain Exists
	function dnshttp_api_domain_id_exists($mysql, $id) { 
		if(is_numeric($id)) { 
			$query = mysqli_query($mysql->mysqlcon, "SELECT * FROM "._TABLE_DOMAIN_BIND_." WHERE id = \"".$mysql->escape($id)."\"");
			while ($result	=	mysqli_fetch_array($query) ) { return true; } 
		} return false; 
	}	
	
	function dnshttp_api_domain_name_exists($mysql, $domain_name) { 
		if(trim($domain_name) != "") { 
			$query = mysqli_query($mysql->mysqlcon, "SELECT * FROM "._TABLE_DOMAIN_BIND_." WHERE domain = \"".$mysql->escape(trim($domain_name))."\"");
		while ($result	=	mysqli_fetch_array($query) ) { return $result["id"]; } 
		
		} return false; 
	}	
	
	// Get all Informations of a Domain
	function dnshttp_api_domain_get($mysql, $id) {
		if(is_numeric($id)) { 
			$query = mysqli_query($mysql->mysqlcon, "SELECT * FROM "._TABLE_DOMAIN_BIND_." WHERE id = \"".$mysql->escape($id)."\"");
			while ($result	=	mysqli_fetch_array($query) ) { return $result; } 
		} return false; 		
	}
	
	// Register a New Domain
	function dnshttp_api_domain_delete_by_id($mysql, $id) {
		// If Exists, Do not Register
		if(dnshttp_bind_id_exists($mysql, $id)) {  
			return $mysql->query("DELETE FROM "._TABLE_DOMAIN_BIND_." WHERE id = '$id';");
		} return false;		
	}
	
	// Register a New Domain
	function dnshttp_api_domain_delete_by_name($mysql, $domain) {
		// If Exists, Do not Register
		if(dnshttp_bind_name_exists($mysql, $domain)) {  
			return $mysql->query("DELETE FROM "._TABLE_DOMAIN_BIND_." WHERE domain = '".$mysql->escape(trim($domain))."';");
		} return false;		
	}
?>