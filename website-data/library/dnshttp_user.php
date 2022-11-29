<?php
	/*
		__________              _____.__       .__     
		\______   \__ __  _____/ ____\__| _____|  |__  
		 |    |  _/  |  \/ ___\   __\|  |/  ___/  |  \ 
		 |    |   \  |  / /_/  >  |  |  |\___ \|   Y  \
		 |______  /____/\___  /|__|  |__/____  >___|  /
				\/     /_____/               \/     \/  MRoD Functions File */

	function dnshttp_user_get_name_from_id($mysql, $userid) { 
		if(is_numeric($userid)) { 
			$query = mysqli_query($mysql->mysqlcon, "SELECT * FROM "._TABLE_USER_." WHERE id = \"".$userid."\"");
			while ($result	=	mysqli_fetch_array($query) ) { return $result["user_name"]; } 
		} return false; 
	}	
	
	function dnshttp_user_get_name_from_id_read($mysql, $userid) { 
		if(is_numeric($userid)) { 
			$query = mysqli_query($mysql->mysqlcon, "SELECT * FROM "._TABLE_USERS_." WHERE id = \"".mysqli_real_escape_string($mysql, $userid)."\"");
		while ($result	=	mysqli_fetch_array($query) ) { return $result["user"]; } 
		
		} return false; 
	}	

	function dnshttp_user_get_id_from_name($mysql, $userid) { 
		if(is_numeric($userid)) { 
			$query = mysqli_query($mysql->mysqlcon, "SELECT * FROM "._TABLE_USERS_." WHERE id = \"".mysqli_real_escape_string($mysql, $userid)."\"");
		while ($result	=	mysqli_fetch_array($query) ) { return $result["user"]; } 
		
		} return false; 
	}	
	
	function dnshttp_user_get_id_from_name_read($mysql, $userid) { 
		if(is_numeric($userid)) { 
			$query = mysqli_query($mysql->mysqlcon, "SELECT * FROM "._TABLE_USERS_." WHERE id = \"".mysqli_real_escape_string($mysql, $userid)."\"");
			while ($result	=	mysqli_fetch_array($query) ) { return $result["user"]; } 
		} return false; 
	}			
?>