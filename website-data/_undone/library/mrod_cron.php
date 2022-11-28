<?php
	/*
		__________              _____.__       .__     
		\______   \__ __  _____/ ____\__| _____|  |__  
		 |    |  _/  |  \/ ___\   __\|  |/  ___/  |  \ 
		 |    |   \  |  / /_/  >  |  |  |\___ \|   Y  \
		 |______  /____/\___  /|__|  |__/____  >___|  /
				\/     /_____/               \/     \/  MRoD Functions File */
	function mrod_cron_registerDomain($mysql, $domain) {
		$done = false;
		$output = array();
		foreach(_CRON_ARRAY_ AS $key => $value) {
			if($value == "dns-sub" AND !$done) {
				if(_CRON_SUB_AS_RELAY_) {
					$done = true;
					$output["type"] = $value;
					$output["port"] = _CRON_SUB_AS_RELAY_PORT_;
					$output["host"] = _CRON_SUB_AS_RELAY_SUB_.".".$domain;
					$output["prot"] = _CRON_SUB_AS_RELAY_PROT_;
					$output["relay"] = "NULL";
				}
			}
			if($value == "dns-txt" AND !$done) {
				if(_CRON_TXT_TO_RELAY_) {
					@ob_start();
					@passthru("/usr/bin/dig ".$domain." TXT");
					@$lookup = @ob_get_contents();
					@ob_end_clean();
					
					if(strpos(@$lookup, _CRON_TXT_TO_RELAY_STRING_) > -1) { 
						$txtrelay = trim(substr($lookup, strpos($lookup, _CRON_TXT_TO_RELAY_STRING_)+strlen(_CRON_TXT_TO_RELAY_STRING_)));
						$txtrelay = trim(substr($txtrelay, 0, strpos($txtrelay, "\"")));
					} 
					if(trim(@$txtrelay) != "" AND is_numeric(@$txtrelay)) {
						if(mrod_relay_id_exists($mysql, $txtrelay)) {
							$infos	=	mrod_relay_get($mysql, $txtrelay);
							$done = true;
							$output["port"] = $infos["port"];
							$output["host"] = $infos["servername"];
							if($infos["smtps"] == 1) { $output["prot"] = "smtps"; } else { $output["prot"] = "smtp"; }
							$output["type"] = $value;	
							$output["relay"] = $txtrelay;										
						}			
					}
				}
			}
			if($value == "dns-dom" AND !$done) {
				if(_CRON_DOMAIN_AS_RELAY_) {
					$done = true;
					$output["type"] = $value;
					$output["port"] = _CRON_DOMAIN_AS_RELAY_PORT_;
					$output["host"] = $domain;
					$output["prot"] = _CRON_DOMAIN_AS_RELAY_PROT_;
					$output["relay"] = "NULL";
				}
			}
		} return $output;
	}
		
	function mrod_cron_writeTransportFile($string) {
		// Delete File If Exists
		if(file_exists(_CRON_TRANSMAP_)) { @unlink(_CRON_TRANSMAP_); }
		
		// Write the New File
		$file = fopen(_CRON_TRANSMAP_, "w") or die("\r\n\r\n### Error! Unable to write file "._CRON_TRANSMAP_."! ");
		if($file) {@fwrite($file, $string);			
			// Output for Status Mail
			echo "\r\n\r\nThis is the Transportmaps File: \r\n";
			echo "--------------------------------------------------\r\n";
			echo $string;
			@fclose($file);
		}	
	}			
		
	function mrod_cron_writeRelayFile($string) {
		// Delete File If Exists
		if(file_exists(_CRON_RELAYMAP_)) { @unlink(_CRON_RELAYMAP_); }
		
		// Write the New File
		$file = fopen(_CRON_RELAYMAP_, "w") or die("\r\n\r\n### Error! Unable to write file "._CRON_RELAYMAP_."! ");
		if($file) {@fwrite($file, $string);			
			// Output for Status Mail
			echo "\r\n\r\nThis is the Relaymaps File: \r\n";
			echo "--------------------------------------------------\r\n";
			echo $string;
			@fclose($file);
		}	
	}
	
?>