<?php
	/*
		__________              _____.__       .__     
		\______   \__ __  _____/ ____\__| _____|  |__  
		 |    |  _/  |  \/ ___\   __\|  |/  ___/  |  \ 
		 |    |   \  |  / /_/  >  |  |  |\___ \|   Y  \
		 |______  /____/\___  /|__|  |__/____  >___|  /
				\/     /_____/               \/     \/  Default Forwarding File for Hidden Areas*/
	require_once("../settings.php");
	if(is_numeric(dnshttp_api_token_relay($mysql, @$_POST["token"])) AND $x = dnshttp_api_domain_name_exists($mysql, @$_POST["domain"])) { 
		$domar	=	array();
		$ar = $mysql->select("SELECT content FROM "._TABLE_DOMAIN_BIND_." WHERE id = ".$x."", false);
		if(is_array($ar)) {
			echo $ar["content"];
		} else { echo serialize(array()); }
	} 
	exit();
?>