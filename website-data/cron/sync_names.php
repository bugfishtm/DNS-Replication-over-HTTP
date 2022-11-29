<?php
	// Configurations Include
		require_once(dirname(__FILE__) ."/../settings.php");
	
		$all_domains_full = array();
		
		$servers = $mysql->select("SELECT * FROM "._TABLE_SERVER_." WHERE server_type = 1", true);
		if(is_array($servers)) {
			foreach($servers as $key => $value) {
				echo "\r\n\r\nChecking Master Server Domain List: ".$value["api_path"]."\r\n";
				echo "--------------------------------\r\n";
				$apipath	=	$value["api_path"]."/api/master_domain_list.php";
				$returncurl =   dnshttp_api_getcontent($mysql, $apipath, $value["api_token"]);
				if($newarray = unserialize($returncurl)) {
					if(is_array($newarray)) {
						$all_domains = array();
						
						foreach($newarray as $x => $y) {
							array_push($all_domains_full,array( trim($y),  $value["id"]) );
							array_push($all_domains,trim($y) );
							$tmp = $mysql->select("SELECT * FROM "._TABLE_DOMAIN_API_." WHERE fk_server = ".$value["id"]." AND domain = '".$mysql->escape(trim($y))."'", false);
							if(is_array($tmp )) {
								$mysql->query("UPDATE "._TABLE_DOMAIN_API_." SET fk_server = '".$value["id"]."', modification = CURRENT_TIMESTAMP() WHERE id = '".$tmp["id"]."';");	
								echo "UPDATE - $y\r\n";
							} else {
								$mysql->query("INSERT INTO "._TABLE_DOMAIN_API_."(domain, content, fk_server)
								VALUES('".$mysql->escape($y)."', '0', '".$value["id"]."');");	
								echo "INSERT - $y\r\n";
							}								
						}
						
						echo "\r\n\r\nCleanup Unused Domains: \r\n";
						echo "--------------------------------------------------\r\n";
						$real_all_domains	= $mysql->select("SELECT * FROM "._TABLE_DOMAIN_API_."", true);	
						if(is_array($real_all_domains)) {
							foreach($real_all_domains as $key => $value) {
								$deleteable = true;
								
								if(is_array($all_domains)) {
									foreach($all_domains as $x => $y) {
										if($y == $value["domain"]) { $deleteable = false; }
									}
								}
								
								if($deleteable) { echo "Domain: ".$value["domain"]."has been deleted!\r\n"; $mysql->query("DELETE FROM "._TABLE_DOMAIN_API_." WHERE id = '".$value["id"]."'"); }
							}
						}						
						
						
					}
				}
			}
		}


			echo "\r\n\r\nChecking for Conflicts in Listing\r\n";
			echo "--------------------------------\r\n";		
		foreach($all_domains_full as $key => $value) {
			$domainname_current = $value[0];
			$conflict = false;
			$conflictarray = array();
			
			foreach($all_domains_full as $x => $y) {
				if($x != $key AND trim($y[0]) == trim($value[0])) {
					$conflict = true;
					array_push($conflictarray, $y[1]);
				} elseif($x == $key) {
					array_push($conflictarray, $y[1]);
				}
			}			
			
			if($conflict) {
				echo "Conflict Domain: $domainname_current\r\n";
				$selector = $mysql->select("SELECT * FROM "._TABLE_CONFLICT_." WHERE domain = '".$mysql->escape(trim($domainname_current))."'", false);
				if(is_array($selector)) {
					$mysql->query("UPDATE "._TABLE_CONFLICT_." SET servers = '".$mysql->escape(serialize($conflictarray))."' WHERE id = '".$selector["id"]."'");
				} else {  $mysql->query("INSERT INTO "._TABLE_CONFLICT_." (domain, servers) VALUES('".$mysql->escape(trim($domainname_current))."', '".$mysql->escape(serialize($conflictarray))."')"); }
			} else { $mysql->query("DELETE FROM "._TABLE_CONFLICT_." WHERE domain = '".$mysql->escape(trim($domainname_current))."'"); }
		}
		
		// Message for Cron
			echo "\r\nCronjob Done! x)\r\n";
	?>
