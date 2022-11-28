<?php
	/*
		__________              _____.__       .__     
		\______   \__ __  _____/ ____\__| _____|  |__  
		 |    |  _/  |  \/ ___\   __\|  |/  ___/  |  \ 
		 |    |   \  |  / /_/  >  |  |  |\___ \|   Y  \
		 |______  /____/\___  /|__|  |__/____  >___|  /
				\/     /_____/               \/     \/  Default Forwarding File for Hidden Areas*/
	require_once("../settings.php");
	$result = $mysql->select("SELECT * FROM "._TABLE_DOMAIN_." WHERE type LIKE '%dns%' ORDER BY creation DESC", false);
	if(is_array($result)) {
		echo $result["creation"];
	}
?>