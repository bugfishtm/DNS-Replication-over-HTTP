<?php
	/*
		__________              _____.__       .__     
		\______   \__ __  _____/ ____\__| _____|  |__  
		 |    |  _/  |  \/ ___\   __\|  |/  ___/  |  \ 
		 |    |   \  |  / /_/  >  |  |  |\___ \|   Y  \
		 |______  /____/\___  /|__|  |__/____  >___|  /
				\/     /_____/               \/     \/  Default Forwarding File for Hidden Areas*/
	require_once("../settings.php");
	if(is_numeric(dnshttp_api_token_relay($mysql, @$_POST["token"])) OR is_numeric(dnshttp_api_token_relay($mysql, @$_GET["token"]))) { 
		$domar	=	array();
		$ar = $mysql->select("SELECT domain FROM "._TABLE_DOMAIN_BIND_."", true);
		if(is_array($ar)) {
			foreach($ar AS $key => $value) {
				array_push($domar, $value["domain"]);
			}
			echo serialize($domar);
		} else { echo serialize(array()); }
	} 
	exit();
?>