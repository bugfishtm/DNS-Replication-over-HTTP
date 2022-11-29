<?php
	// Configurations Include
		require_once(dirname(__FILE__) ."/../settings.php");
	
		$all_domains = array();
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//// Fetch Entries from Cached File Names		
		/*	if ($handle = opendir(_CRON_BIND_LIB_)) {
				while (false !== ($entry = readdir($handle))) {
					if ($entry != "." && $entry != "..") {
						if (strpos($entry, "_default.nzd") === false AND strpos($entry, "keys.bind") === false) {	
							$domain = trim($entry);
							if(trim($domain) != "") {
								if($x = dnshttp_bind_domain_name_exists($mysql, trim($domain))) {
									$mysql->query("UPDATE "._TABLE_DOMAIN_BIND_." SET content = '0', modification = CURRENT_TIMESTAMP() WHERE id = '".$x."';");	
									echo "UPDATE - $domain\r\n";
								} else {
									$mysql->query("INSERT INTO "._TABLE_DOMAIN_BIND_."(domain, content)
									VALUES('".$mysql->escape($domain)."', '0');");				
									echo "INSERT - $domain\r\n";
								}							
							}
						}
					}
				}
				closedir($handle);
				echo _TITLE_.": Success Fetching DNS Entries from "._CRON_BIND_LIB_."\r\n\r\n";
			} else {  echo _TITLE_.": Failed to Fetch DNS Entries "._CRON_BIND_LIB_."\r\n\r\n"; } 
			
			if ($handle = opendir(_CRON_BIND_CACHE_)) {
				while (false !== ($entry = readdir($handle))) {
					if ($entry != "." && $entry != "..") {
						if (strpos($entry, "_default.nzd") === false AND strpos($entry, "keys.bind") === false) {	
							$domain = trim($entry);
							if(trim($domain) != "") {
								if($x = dnshttp_bind_domain_name_exists($mysql, trim($domain))) {
									$mysql->query("UPDATE "._TABLE_DOMAIN_BIND_." SET content = '0', modification = CURRENT_TIMESTAMP() WHERE id = '".$x."';");
									echo "UPDATE - $domain\r\n";
								} else {
									$mysql->query("INSERT INTO "._TABLE_DOMAIN_BIND_."(domain, content)
									VALUES('".$mysql->escape($domain)."', '0');");	
									echo "INSERT - $domain\r\n";									
								}							
							}
						}
					}
				}
				closedir($handle);
				echo _TITLE_.": Success Fetching DNS Entries from "._CRON_BIND_CACHE_."\r\n\r\n";
			} else {  echo _TITLE_.": Failed to Fetch DNS Entries "._CRON_BIND_CACHE_."\r\n\r\n"; } */
			
			$handle = fopen(_CRON_BIND_FILE_, "r"); if ($handle) {
				while (($line = fgets($handle)) !== false) {
					if(strpos($line, "zone ") > -1 AND strpos($line, ".in-addr.arpa") === false AND strpos($line, "localhost") === false) {
						preg_match('/"(.*?)"/', $line, $match);
						$domain = trim($match[1]);
						
							if(trim($domain) != "") {
								array_push($all_domains,trim($domain) );
								if($x = dnshttp_bind_domain_name_exists($mysql, trim($domain))) {
									$gg = fopen(_CRON_BIND_LIB_. trim($domain) .'.hosts', 'r') or die("File for $domain not found! \r\n");
									$readtext = fread($gg, filesize(_CRON_BIND_LIB_. trim($domain) .'.hosts'));
									$bind[0]["type"] = "s";
									$bind[0]["value"] = $readtext;
									$mysql->query("UPDATE "._TABLE_DOMAIN_BIND_." SET content = ?, modification = CURRENT_TIMESTAMP() WHERE id = '".$x."';", $bind);	
									echo "UPDATE - $domain\r\n";
								} else {
									$mysql->query("INSERT INTO "._TABLE_DOMAIN_BIND_."(domain, content)
									VALUES('".$mysql->escape($domain)."', '0');");	
									echo "INSERT - $domain\r\n";
								}									
							}
					}
				}
				fclose($handle); echo _TITLE_.": Success Fetching DNS Entries from "._CRON_BIND_FILE_."\r\n\r\n";
			} else {  echo _TITLE_.": Failed to Fetch DNS Entries "._CRON_BIND_FILE_."\r\n\r\n"; } 


			echo "\r\n\r\nCleanup Unused Domains: \r\n";
			echo "--------------------------------------------------\r\n";
			$real_all_domains	= $mysql->select("SELECT * FROM "._TABLE_DOMAIN_BIND_."", true);	
			if(is_array($real_all_domains)) {
				foreach($real_all_domains as $key => $value) {
					$deleteable = true;
					
					if(is_array($all_domains)) {
						foreach($all_domains as $x => $y) {
							if($y == $value["domain"]) { $deleteable = false; }
						}
					}
					
					if($deleteable) { echo "Domain: ".$value["domain"]."has been deleted!\r\n"; $mysql->query("DELETE FROM "._TABLE_DOMAIN_BIND_." WHERE id = '".$value["id"]."'"); }
				}
			}
		
		// Message for Cron
			echo "\r\nCronjob Done! x)\r\n";
	?>
