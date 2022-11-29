<?php
	// Configurations Include
		require_once(dirname(__FILE__) ."/../settings.php");
		
		$domains = $mysql->select("SELECT * FROM "._TABLE_DOMAIN_API_." WHERE content <> '0' ORDER BY modification DESC", true);
		if(is_array($domains)) {

			// ###################################################################################################
			// Write the dnshttp.conf.local FILE
				$conf_buildstring = "";
				foreach($domains as $key => $value) {
					$relay = dnshttp_server_get($mysql, $value["fk_server"]);
					var_dump($value["fk_server"]);
					$conf_buildstring .= '

zone "'.$value["domain"].'" {
	type slave;
	masters {'.$relay["ip"].'; };
	allow-transfer { '.$relay["ip"].'; };
	file "'._CRON_BIND_LIB_.$value["domain"].'.hosts";
};	

';}	
			// ###################################################################################################
			// Write the Domain Files				
				foreach($domains as $key => $value) {				
					if(file_exists(_CRON_BIND_LIB_."/".$value["domain"].".hosts")) { unlink(_CRON_BIND_LIB_."/".$value["domain"].".hosts"); } 
					file_put_contents(_CRON_BIND_LIB_."/".$value["domain"].".hosts", $value["content"]);
					echo "Writing for Domain "._CRON_BIND_LIB_."/".$value["domain"].".hosts"."\r\n";
				}
			echo "\r\nWrite Bind DNSHTTP Config File\r\n";
			echo "----------------\r\n";				
			// Last Steps for File Writing
				if(file_exists(_CRON_BIND_FILE_DNSHTTP_)) { unlink(_CRON_BIND_FILE_DNSHTTP_); }
				file_put_contents(_CRON_BIND_FILE_DNSHTTP_, $conf_buildstring);
		}
		
		// ###################################################################################################
		// DELETE OLD ENTRIES
			$domains_binded = $mysql->select("SELECT * FROM "._TABLE_DOMAIN_BINDED_."", true);
			$domains = $mysql->select("SELECT * FROM "._TABLE_DOMAIN_API_." WHERE content <> '0' ORDER BY modification DESC", true);
			if(is_array($domains)) {	
				if(is_array($domains_binded)) {
					foreach($domains_binded as $key => $value) {
						$delete = true;
						foreach($domains as $y => $x) {
							if($x["domain"] == $value["domain"])  { $delete = false; } 
						}
						
						if($delete) { 
							if(file_exists(_CRON_BIND_LIB_."/".$value["domain"].".hosts")) {
								unlink(_CRON_BIND_LIB_."/".$value["domain"].".hosts");
							}
							$mysql->query("DELETE FROM "._TABLE_DOMAIN_BINDED_." WHERE domain = '".$mysql->escape($value["domain"])."'");
							echo "Deleted Domain ".$value["domain"]."\r\n";
						}
					}
				}
				
				foreach($domains as $key => $value) {
					$inserted = false;
					if(is_array($domains_binded)) {
						foreach($domains_binded as $y => $x) {
							if($x["domain"] == $value["domain"])  { $inserted = true; } 
						}
					}
					if(!$inserted) {
						$mysql->query("INSERT INTO "._TABLE_DOMAIN_BINDED_." (domain) VALUES('".$mysql->escape(trim($value["domain"]))."');");
						echo "Inserted Domain ".$value["domain"]."\r\n";
					}
				} 
				
			}

		// Message for Cron
			echo "\r\nCronjob Done! x)\r\n";
	?>
