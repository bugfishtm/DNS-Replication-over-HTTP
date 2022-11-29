<?php
	/*
		__________              _____.__       .__     
		\______   \__ __  _____/ ____\__| _____|  |__  
		 |    |  _/  |  \/ ___\   __\|  |/  ___/  |  \ 
		 |    |   \  |  / /_/  >  |  |  |\___ \|   Y  \
		 |______  /____/\___  /|__|  |__/____  >___|  /
				\/     /_____/               \/     \/  Default Forwarding File for Hidden Areas*/
	require_once("../settings.php");
	if(is_numeric(dnshttp_api_token_relay($mysql, @$_POST["token"]))) { 
	echo "online"; } else { echo "tokenerror"; }
	exit();
?>