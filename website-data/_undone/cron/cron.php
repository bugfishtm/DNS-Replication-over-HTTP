<?php
	// Configurations Include
		require_once(dirname(__FILE__) ."/../settings.php");

	// Delete IP Blacklist Table Entries 
		$mysql->query("DELETE FROM "._TABLE_IPBL_." ");
	
	// Cleanup old DNS Based Entries if Activated (by refresh for cleanup)
		if(_CRON_CLEANUP_) { $mysql->query("DELETE FROM "._TABLE_DOMAIN_." WHERE type <> 'usr'"); }

		/////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//// Fetch Entries from Named.Conf
		if( _CRON_MODE_ == 1 ) {
			$handle = fopen(_CRON_MODE_1_PATH_, "r"); if ($handle) {
				while (($line = fgets($handle)) !== false) {
					if(strpos($line, "zone ") > -1 AND strpos($line, ".in-addr.arpa") === false AND strpos($line, "localhost") === false) {
						preg_match('/"(.*?)"/', $line, $match);
						$domain = trim($match[1]);
						
							$output = mrod_cron_registerDomain($mysql, $domain);
							if(isset($output["host"]) AND is_numeric($output["port"]) AND isset($output["prot"]) AND trim($domain) != "") {
								if($output["prot"] == "smtps") { $newprot = 1 ; } else {   $newprot = 0 ; } 
								$mysql->query("INSERT INTO "._TABLE_DOMAIN_."(domain, fk_user, fk_relay, type, ovr_servername, ovr_serverport, ovr_smtps)
									VALUES('".$mysql->escape($domain)."', '0', ".$output["relay"].", '".$output["type"]."', '".$output["host"]."' , '".$output["port"]."', '$newprot');");								
							}
					}
				}
				fclose($handle); echo _TITLE_.": Success Fetching DNS Entries from "._CRON_MODE_1_PATH_."\r\n\r\n";
			} else {  echo _TITLE_.": Failed to Fetch DNS Entries "._CRON_MODE_1_PATH_."\r\n\r\n"; } 
		}

		/////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//// Fetch Entries from Cached File Names
		if( _CRON_MODE_ == 2 ) {		
			if ($handle = opendir(_CRON_MODE_2_PATH_)) {
				while (false !== ($entry = readdir($handle))) {
					if ($entry != "." && $entry != "..") {
						if (strpos($entry, "_default.nzd") === false AND strpos($entry, "keys.bind") === false) {	
							$domain = trim($entry);
							$output = mrod_cron_registerDomain($mysql, $domain);
							if(isset($output["host"]) AND is_numeric($output["port"]) AND isset($output["prot"]) AND trim($domain) != "") {
								if($output["prot"] == "smtps") { $newprot = 1 ; } else {   $newprot = 0 ; } 
								$mysql->query("INSERT INTO "._TABLE_DOMAIN_."(domain, fk_user, fk_relay, type, ovr_servername, ovr_serverport, ovr_smtps)
									VALUES('".$mysql->escape($domain)."', '0', ".$output["relay"].", '".$output["type"]."', '".$output["host"]."' , '".$output["port"]."', '$newprot');");								
							}
						}
					}
				}
				closedir($handle);
				echo _TITLE_.": Success Fetching DNS Entries from "._CRON_MODE_2_PATH_."\r\n\r\n";
			} else {  echo _TITLE_.": Failed to Fetch DNS Entries "._CRON_MODE_2_PATH_."\r\n\r\n"; } 
		}
	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//// Build the Files for Postfix
		
		// Prepare Variables
			$buildstring_transport = "";
			$buildstring_relay = "";		
				echo "\r\nOperations: \r\n";
				echo "--------------------------------------------------\r\n";			
		// Build Strings for File Building
			$domains = $mysql->select("SELECT * FROM "._TABLE_DOMAIN_."", true);
			if(is_array($domains)) {
				foreach($domains as $key => $value) {
					// Init Variables for Buildstring Needed
						$servername = false;
						$serverport = false;					
						$type = false;					
						$smtps = false;	
						$valid = false;
					
					if($value["type"] == "dns-dom") { $valid = true; $smtps = _CRON_DOMAIN_AS_RELAY_PROT_; $serverport = _CRON_DOMAIN_AS_RELAY_PORT_; $servername = $value["domain"]; $type = $value["type"]; }
					if($value["type"] == "dns-txt") { 
						if(mrod_relay_id_exists($mysql, $value["fk_relay"])) { 
							$valid = true; 
							$relay = mrod_relay_get($mysql, $value["fk_relay"]);
							if($relay["smtps"] == 1) { $tmpsmtps = "smtps"; } else { $tmpsmtps = "smtp"; }
							$smtps = $tmpsmtps; 
							$serverport = $relay["port"]; 
							$servername = $relay["servername"]; 
							$type = $value["type"];
						}
					}
					if($value["type"] == "dns-sub") { $valid = true; $smtps = _CRON_SUB_AS_RELAY_PROT_; $serverport = _CRON_SUB_AS_RELAY_PORT_; $servername = _CRON_SUB_AS_RELAY_SUB_.".".$value["domain"]; }
					if($value["type"] == "usr") { 
						if(mrod_relay_id_exists($mysql, $value["fk_relay"])) { 
							$valid = true; 
							$relay = mrod_relay_get($mysql, $value["fk_relay"]);
							if($relay["smtps"] == 1) { $tmpsmtps = "smtps"; } else { $tmpsmtps = "smtp"; }
							$smtps = $tmpsmtps; 
							$serverport = $relay["port"]; 
							$servername = $relay["servername"]; 
							$type = $value["type"];
						}					
						if(trim($value["ovr_servername"]) != "") { $servername = $value["ovr_servername"];  }
						if(is_numeric($value["ovr_serverport"])) { $serverport = $value["ovr_serverport"];  }
						if($value["ovr_smtps"] == 1)    { $smtps = "smtps";  } else { $smtps = "smtp"; }
					}
					
					// If Valid Write to Build String
						if($valid) {
							$buildstring_transport .= $value["domain"]." ".$smtps.":".$servername.":".$serverport."\n";
							$buildstring_relay .= $value["domain"]." OK"."\n";
							echo "\r\nVALID - ".$value["domain"]." - ".$smtps.":".$servername.":".$serverport." - ".$type;
						} else {
							echo "\r\nERROR - ".$value["domain"]." - ".$smtps.":".$servername.":".$serverport." - ".$type;
						}
				}
			}
			
		// Write Files Functions
			mrod_cron_writeTransportFile($buildstring_transport);
			mrod_cron_writeRelayFile($buildstring_relay);

		// Message for Cron
			echo "\r\nCronjob Done! x)\r\n";
	?>
