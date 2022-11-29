<?php
	/*
		__________              _____.__       .__     
		\______   \__ __  _____/ ____\__| _____|  |__  
		 |    |  _/  |  \/ ___\   __\|  |/  ___/  |  \ 
		 |    |   \  |  / /_/  >  |  |  |\___ \|   Y  \
		 |______  /____/\___  /|__|  |__/____  >___|  /
				\/     /_____/               \/     \/  API Functions File */
		function dnshttp_api_token_generate($len = 32, $comb = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890')  
			{$pass = array(); $combLen = strlen($comb) - 1; for ($i = 0; $i < $len; $i++) { $n = mt_rand(0, $combLen); $pass[] = $comb[$n]; } return implode($pass);}			
				
		function dnshttp_api_token_create($mysql, $relay) {
			$token = dnshttp_api_token_generate();
			$bind[0]["type"]	=	"s";
			$bind[0]["value"]	=	trim($token);
			$res = $mysql->select("SELECT * FROM "._TABLE_SERVER_." WHERE api_token = ?", false, $bind);
			if(is_array($res)) {dnshttp_api_token_create($mysql, $relay); }  else { $mysql->query("UPDATE "._TABLE_SERVER_." SET api_token = '".$token ."' WHERE id = '".$relay."'", false, $bind); return true; }				
		}
		
		function dnshttp_api_token_check($mysql, $token) {
			$bind[0]["type"]	=	"s";
			$bind[0]["value"]	=	trim($token);
			$res = $mysql->select("SELECT * FROM "._TABLE_SERVER_." WHERE api_token = ?", false, $bind);
			if(is_array($res)) { return true; }  else { return false; }				
		}
		
		function dnshttp_api_token_relay($mysql, $token) {
			$bind[0]["type"]	=	"s";
			$bind[0]["value"]	=	trim($token);
			$res = $mysql->select("SELECT * FROM "._TABLE_SERVER_." WHERE api_token = ?", false, $bind);
			if(is_array($res)) { return $res["id"];}  else { return false; }	
		}
		
		function dnshttp_api_getcontent($mysql, $url, $token = false, $domain = "") {
			if(is_string($token)) {
			  $fields = array(
				'token'=>urlencode($token),
				'domain'=>urlencode(trim($domain)),
			  );			
				$fields_string = "";
			  //url-ify the data for the POST
			  foreach($fields as $key=>$value) { @$fields_string .= $key.'='.$value.'&'; }
			  rtrim($fields_string,'&');
			}
			  // Initialize curl
			  $ch = curl_init();

			  //set the url, number of POST vars, POST data
			  curl_setopt($ch,CURLOPT_URL,$url);
			  if(is_string($token)) {curl_setopt($ch,CURLOPT_POST,count($fields));}
			  if(is_string($token)) {curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);}
			  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
			  curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);				
			  //execute post
			  $result = curl_exec($ch);

			  return $result;
		}
?>