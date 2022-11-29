<?php
	// Configurations Include
		require_once(dirname(__FILE__) ."/../settings.php");
		
		$domains = $mysql->select("SELECT * FROM "._TABLE_DOMAIN_API_." ORDER BY modification DESC", true);
		if(is_array($domains)) {
			foreach($domains as $key => $value) {
				$apipath	=	dnshttp_server_get($mysql, $value["fk_server"])["api_path"]."/api/master_domain_content.php";
				$returncurl =   dnshttp_api_getcontent($mysql, $apipath, dnshttp_server_get($mysql, $value["fk_server"])["api_token"], $value["domain"]);
				$domain = $value["domain"];
				if($returncurl) {
					if(strpos($returncurl, "SOA") > 5) {
						$bind[0]["type"] = "s";
						$bind[0]["value"] = $returncurl;
						$mysql->query("UPDATE "._TABLE_DOMAIN_API_." SET content = ?, modification = CURRENT_TIMESTAMP() WHERE id = '".$value["id"]."';", $bind);
						echo "SUCCESS: $domain \r\n";							
					} else { echo "Could not Fetch Entries for $domain \r\n"; }
				} else { echo "Could not Fetch Content for $domain \r\n"; }
			}
		}
		
		// Message for Cron
			echo "\r\nCronjob Done! x)\r\n";
	?>
