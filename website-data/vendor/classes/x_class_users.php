<?php
	/*	__________ ____ ___  ___________________.___  _________ ___ ___  
		\______   \    |   \/  _____/\_   _____/|   |/   _____//   |   \ 
		 |    |  _/    |   /   \  ___ |    __)  |   |\_____  \/    ~    \
		 |    |   \    |  /\    \_\  \|     \   |   |/        \    Y    /
		 |______  /______/  \______  /\___  /   |___/_______  /\___|_  / 
				\/                 \/     \/                \/       \/  Login and Users Control Class	*/
	
	class x_class_users {    
		// MySQL Runtime Variables for Inside this Class which are private
			private $mysqlcon	 						= false;
			private $table_sessions 					= false;
			private $table_users 						= false;

		// Key Types Variables for Session Database Identification which are private
			private $key_session						= 1; // Login Session
			private $key_recover						= 2; // Recovers and Activation
			private $key_mail_change					= 3; // Mail Changes with Confirmations

		// Return Codes for Mail and Requests which are private
			public $rec_request_code					= false;
			public $rec_confirm_code					= false;
			public $login_request_code					= false;
			public $user_mailchange_confirm_code		= false;
			
		// Return Codes for Mail and Requests which are private				
			public $mail_ref_token						= false;
			public $mail_ref_user						= false;
			public $mail_ref_receiver					= false;
			public $mail_ref_receiver_name				= false;
		
		// User Variables to be Called in Scripts for User Informations which are public
			public $user	 							= false;
			public $user_id 							= false;
			public $user_session						= false;		
			public $user_rank						    = false;		
			public $user_name						    = false;			
			public $user_mail						    = false;			
			public $user_loggedIn					    = false;		
			public $loggedIn					   		= false;		
		
		// User Control Class Configurations
			private $cookies_pre 	 	 = "x_users"; // Changed in Constructor
			private $cookies_use 	= false;   public function cookies_use($bool = false)  {$this->cookies_use = $bool;}
			private $multi_login	 = false; public function multi_login($bool = false)	{$this->multi_login	 	= $bool;}
			private $log_sessions 	 = false; public function log_sessions($bool = false)	{$this->log_sessions	= $bool;}	
			private $log_recovers	 = false; public function log_recovers($bool = false) 	{$this->log_recovers	= $bool;}
			private $log_user_mailchange = false; public function log_user_mailchange($bool = false){$this->log_user_mailchange	= $bool;}	
			private $reset_token_login 	 = false;  public function reset_token_login($bool = false)  {$this->reset_token_login = $bool;}
			private $save_ip_in_db = false;	public function save_ip_in_db($bool = false) {$this->save_ip_in_db = $bool;}	
			private $relevant_reference_username = "user_mail"; public function relevant_reference_username($bool = false) {if($bool) {$this->relevant_reference_username = "user_name";} else {$this->relevant_reference_username	 = "user_mail";}}
			private $cookies_use_expire_d = 7; public function cookies_use_expire_d($int = 7) {$this->cookies_use_expire_d = $int;}
			private $dbsession_max_use_days = 7; public function dbsession_max_use_days($int = 7) {$this->dbsession_max_use_days = $int;}				
			private $recover_intervall_hours = 6; public function recover_intervall_hours($int = 6) { $this->recover_intervall_hours = $int; }
			private $mailchange_intervall_hours = 6; public function mailchange_intervall_hours($int = 6) { $this->mailchange_intervall_hours = $int; }
			private $recover_token_lifetime_hours = 24; public function recover_token_lifetime_hours($int = 24){$this->recover_token_lifetime_hours = $int;}
			private $usermailchange_token_lifetime_hours = 24; public function usermailchange_token_lifetime_hours($int = 24){$this->usermailchange_token_lifetime_hours = $int;}
		// Generate Keys and Check Password Functions
			public function genKey($len = 12, $comb = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890')  
				{$pass = array(); $combLen = strlen($comb) - 1; for ($i = 0; $i < $len; $i++) { $n = mt_rand(0, $combLen); $pass[] = $comb[$n]; } return implode($pass);}					
			public function passCrypt($var)	{return password_hash($var,PASSWORD_BCRYPT);}
			public function passCheck($cleartext, $crypted)	{return password_verify($cleartext,$crypted);}				
			
			
		// Session Ban Functions
			private $sessionban_limit = false;
			public function isBanned() {if(is_numeric($this->sessionban_limit)){if($_SESSION[$this->cookies_pre."x_users_sec"] > $this->sessionban_limit){return true;} else{return false;}} else {return false;}}
			public function sessionban_limit($int = 50) {$this->sessionban_limit = $int;}
			public function resetFailure() {$_SESSION[$this->cookies_pre."x_users_sec"] = 0;} 
			public function raiseFailure(){if(is_numeric($this->sessionban_limit)) {if(@!is_numeric($_SESSION[$this->cookies_pre."x_users_sec"])){$_SESSION[$this->cookies_pre."x_users_sec"] = 0;} $_SESSION[$this->cookies_pre."x_users_sec"] = $_SESSION[$this->cookies_pre."x_users_sec"] + 1;} else {$this->resetFailure();}} 				
			
		// User Operations
			// Disable Sessions From a User
			public function disableSessionsFrom($userid)  
			{ return $this->mysqlcon->query("UPDATE ".$this->table_sessions." SET is_active = 0 WHERE key_type = '".$this->key_session."' AND user_id = '".mysqli_real_escape_string($this->mysqlcon, $userid)."'"); }				
			// Delete Sessions From a User
			public function deleteSessionsFrom($userid)  
			{return $this->mysqlcon->query("DELETE FROM ".$this->table_sessions." WHERE '".$this->key_session."' AND user_id = '".mysqli_real_escape_string($this->mysqlcon, $userid)."'");}
			// Delete A user
			public function deleteUser($id) { $this->mysqlcon->query("DELETE FROM ".$this->table_sessions." WHERE user_id = '".mysqli_real_escape_string($this->mysqlcon, $id)."'");
				return $this->mysqlcon->query("DELETE FROM ".$this->table_users." WHERE id = '".mysqli_real_escape_string($this->mysqlcon, $id)."'");}						
			// Disable A User or Disable Sessions
			public function disableUser($id) 		  
			{ return $this->mysqlcon->query("UPDATE ".$this->table_users." SET is_blocked = 1 WHERE id = '".mysqli_real_escape_string($this->mysqlcon, $id)."'"); }		
			// Enable A User or Disable Sessions
			public function enableUser($id) 		  
			{ return $this->mysqlcon->query("UPDATE ".$this->table_users." SET is_blocked = 0 WHERE id = '".mysqli_real_escape_string($this->mysqlcon, $id)."'"); }	
			// Get Array with all Table Fields From User
			public function getInfo($id = false) {
				if(is_numeric($id)) { 
					$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_users." WHERE id = '".$id."'"); return mysqli_fetch_array($r); 	
				} else { 
					$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_users." WHERE id = '".$this->user_id."'"); return mysqli_fetch_array($r); 	
				}
			}
			// Disable A User or Disable Sessions
			public function deactivateUser($id) 		  
			{ return $this->mysqlcon->query("UPDATE ".$this->table_users." SET is_confirmed = 0 WHERE id = '".mysqli_real_escape_string($this->mysqlcon, $id)."'"); }		
			// Enable A User or Disable Sessions
			public function activateUser($id) 		  
			{ return $this->mysqlcon->query("UPDATE ".$this->table_users." SET is_confirmed = 1 WHERE id = '".mysqli_real_escape_string($this->mysqlcon, $id)."'"); }				
			// Check if User is Disabled / Blocked
			public function isBlocked($id)  {
				if(is_numeric($id)) {
					$curref =  $new;
					$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_users." WHERE LOWER(id) = '".$id."'");
					if($x = mysqli_fetch_array($r)){ if($x["is_blocked"] != 1) { return false; } return true;} 
					return true;
				} return true; }			
			// Check if User is Disabled / Blocked
			public function isActivated($id)  {
				if(is_numeric($id)) {
					$curref =  $new;
					$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_users." WHERE LOWER(id) = '".$id."'");
					if($x = mysqli_fetch_array($r)){ if($x["is_confirmed"] != 1) { return false; } return true;} 
					return true;
				} return true; }	
			// Change a Users Rank
			public function changeUserRank($id, $new) 
			{return $this->mysqlcon->query("UPDATE ".$this->table_users." SET user_rank = '".mysqli_real_escape_string($this->mysqlcon, $new)."' WHERE id = '".mysqli_real_escape_string($this->mysqlcon, $id)."'");}					
			// Change a Users Password
			public function changeUserPass($id, $new) 
			{return $this->mysqlcon->query("UPDATE ".$this->table_users." SET user_pass = '".mysqli_real_escape_string($this->mysqlcon, $this->passCrypt($new))."' WHERE id = '".mysqli_real_escape_string($this->mysqlcon, $id)."'");}			
			// Add a new User
			public function addUser($name, $mail, $password = false, $rank = false, $activated = false, $delunconfirmedwhennew = false) {
				if($this->relevant_reference_username == "user_mail") { $ref = $mail; } else { $ref = $name;  }
				$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_users." WHERE LOWER(".$this->relevant_reference_username.") = '".strtolower(trim($ref))."'");
				if($rrx = mysqli_fetch_array($r)){ if($rrx["is_confirmed"] == 1) {return false;} else { if($delunconfirmedwhennew) {$this->deleteUser($rrx["id"]);} else { return false; } }} 
				if(!$activated) {$activated = 0;} else {$activated = 1;}
				if(!$rank) {$rankx = 0;} else {$rankx = $rank;}
				if(!$password) {$pass = $this->genKey(32);} else {$pass = $password;}
				return $this->mysqlcon->query("INSERT INTO ".$this->table_users."(user_name, user_mail, user_pass, user_rank, is_confirmed)
				VALUES('".mysqli_real_escape_string($this->mysqlcon, $name)."', '".mysqli_real_escape_string($this->mysqlcon, $mail)."', '". $this->passCrypt($pass)."', '".$rankx."', '".$activated."')");}
			// Check if a User Exists
			public function exists($id) {
				if(!is_numeric($id)) { return false; }
				$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_users." WHERE id = '".$id."'");
				if($rrx = mysqli_fetch_array($r)){ return true; } return false;}
			// Check if a Username Exists
			public function usernameExists($username) {
				$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_users." WHERE LOWER(user_name) = '".mysqli_real_escape_string($this->mysqlcon, strtolower(trim($username)))."'");
				if($rrx = mysqli_fetch_array($r)){ return true; } return false;}
			// Check if a Username Exists Active Confirmed
			public function usernameExistsActive($username) {
				$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_users." WHERE LOWER(user_name) = '".mysqli_real_escape_string($this->mysqlcon, strtolower(trim($username)))."' AND is_confirmed = 1");
				if($rrx = mysqli_fetch_array($r)){ return true; } return false;	}
			// Check if a Mail Exists
			public function mailExists($mail) {
				$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_users." WHERE LOWER(user_mail) = '".mysqli_real_escape_string($this->mysqlcon, strtolower(trim($mail))."'"));
				if($rrx = mysqli_fetch_array($r)){ return true; } return false;	}
			// Check if a Mail Exists Active Confirmed
			public function mailExistsActive($mail) {
				$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_users." WHERE LOWER(user_mail) = '".mysqli_real_escape_string($this->mysqlcon, strtolower(trim($mail)))."' AND is_confirmed = 1");
				if($rrx = mysqli_fetch_array($r)){ return true; } return false;	}
			// Check if a Ref Exists
			public function refExists($ref) {
				$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_users." WHERE LOWER(".$this->relevant_reference_username.") = '".mysqli_real_escape_string($this->mysqlcon, strtolower(trim($ref)))."'");
				if($rrx = mysqli_fetch_array($r)){ return true; } return false;	}
			// Check if a Ref Exists Active Confirmed
			public function refExistsActive($ref) {
				$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_users." WHERE LOWER(".$this->relevant_reference_username.") = '".mysqli_real_escape_string($this->mysqlcon, strtolower(trim($ref)))."'");
				if($rrx = mysqli_fetch_array($r)){ if($rrx["is_confirmed"] == 1) {return true;} else { return false; }} return false;}	
			// Change a Users Username
			public function changeUserName($id, $new)  {
				if ($this->relevant_reference_username != "user_name") {
						$curref =  $new;			
						return $this->mysqlcon->query("UPDATE ".$this->table_users." SET user_name = '".mysqli_real_escape_string($this->mysqlcon, trim($new))."' WHERE id = '".mysqli_real_escape_string($this->mysqlcon, $id)."'"); 					
				} else {
					$isssame = false;
					$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_users." WHERE id = '".mysqli_real_escape_string($this->mysqlcon, $id)."'");
					if($rrx = mysqli_fetch_array($r)){ if(strtolower(trim($rrx["user_name"])) == strtolower(trim($new))) {$isssame = true;}}
					
					if($isssame) {
						return true;
					} elseif($this->usernameExistsActive($new)) {
						return false;					
					} else {
						$curref =  $new;	
						return $this->mysqlcon->query("UPDATE ".$this->table_users." SET user_name = '".mysqli_real_escape_string($this->mysqlcon, trim($curref))."' WHERE id = '".mysqli_real_escape_string($this->mysqlcon, $id)."'"); 					
					}
				}								
			}
			// Change a Users Mail Address
			public function changeUserMail($id, $new)  {
				if ($this->relevant_reference_username != "user_mail") {
						$curref =  $new;			
						return $this->mysqlcon->query("UPDATE ".$this->table_users." SET user_mail = '".mysqli_real_escape_string($this->mysqlcon, trim($new))."' WHERE id = '".mysqli_real_escape_string($this->mysqlcon, $id)."'"); 					
				} else {
					$isssame = false;
					$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_users." WHERE id = '".mysqli_real_escape_string($this->mysqlcon, $id)."'");
					if($rrx = mysqli_fetch_array($r)){ if(strtolower(trim($rrx["user_mail"])) == strtolower(trim($new))) {$isssame = true;}}
					
					if($isssame) {
						return true;
					} elseif($this->mailExistsActive($new)) {
						return false;					
					} else {
						$curref =  $new;	
						return $this->mysqlcon->query("UPDATE ".$this->table_users." SET user_mail = '".mysqli_real_escape_string($this->mysqlcon, trim($curref))."' WHERE id = '".mysqli_real_escape_string($this->mysqlcon, $id)."'"); 					
					}
				}				
			}		
			// Change a Users Shadow Mail Address
			public function changeUserShadowMail($id, $new)  {
				if ($this->relevant_reference_username != "user_mail") {
						$curref =  $new;			
						return $this->mysqlcon->query("UPDATE ".$this->table_users." SET user_mail_shadow = '".mysqli_real_escape_string($this->mysqlcon, trim($new))."' WHERE id = '".mysqli_real_escape_string($this->mysqlcon, $id)."'"); 					
				} else {
					if($this->mailExistsActive($new)) {
						return false;
					} else {
						$curref =  $new;	
						return $this->mysqlcon->query("UPDATE ".$this->table_users." SET user_mail_shadow = '".mysqli_real_escape_string($this->mysqlcon, trim($curref))."' WHERE id = '".mysqli_real_escape_string($this->mysqlcon, $id)."'"); 					
					}
				}
			}

		// Functions for Real Cookie Control of Logins [Not Described in Function Lists]
			private function cookie_add($userid, $sessionkey) { if($this->cookies_use) {
				setcookie($this->cookies_pre."session_userid", $userid, time() + $this->cookies_use_expire_d * 24 * 60 * 60);
				setcookie($this->cookies_pre."session_key", $sessionkey, time() + $this->cookies_use_expire_d * 24 * 60 * 60);}}	
			private function cookie_delete() { if($this->cookies_use) { unset($_COOKIE[$this->cookies_pre.'session_key']); @setcookie($this->cookies_pre.'session_key', '', time() - 3600, '/'); 
				unset($_COOKIE[$this->cookies_pre.'session_userid']); @setcookie($this->cookies_pre.'session_userid', '', time() - 3600, '/');}}
			private function cookie_restore_session() { if($this->cookies_use AND !$this->user_loggedIn) {
				if(isset($_COOKIE[$this->cookies_pre."session_userid"]) AND isset($_COOKIE[$this->cookies_pre."session_key"])) {
					if($this->session_valid($_COOKIE[$this->cookies_pre."session_key"], $_COOKIE[$this->cookies_pre."session_userid"])) {
						$_SESSION[$this->cookies_pre."x_users_key"];
						$_SESSION[$this->cookies_pre."x_users_id"];
						$_SESSION[$this->cookies_pre."x_users_ip"]  = @$_SERVER["REMOTE_ADDR"];
						$this->session_restore();
					 return true;} else { $this->cookie_delete();}} return false; } return true; }
		
		// Functions for Session Control of Logins [Not Described in Function Lists]
			// Restore A Session For Login
			private function session_restore() {
			$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_users." WHERE is_confirmed = '1' AND (is_blocked IS NULL OR is_blocked = 0) AND id = '".mysqli_real_escape_string($this->mysqlcon, $_SESSION[$this->cookies_pre."x_users_id"])."'");
			if($cr = mysqli_fetch_array($r)){
				$this->user_id 		 = $cr["id"];
				$this->user_session	 = $_SESSION[$this->cookies_pre."x_users_key"];		
				$this->user_rank	 = $cr["user_rank"];		
				$this->user 	 	 = $cr;		
				$this->user_name	 = $cr["user_name"];			
				$this->user_mail	 = $cr["user_mail"];
				$this->user_loggedIn = true;	
				$this->loggedIn = true;	
				// Update last Session Use
				$this->mysqlcon->query("UPDATE ".$this->table_sessions." SET use_date = CURRENT_TIMESTAMP() WHERE id = '".$cr["id"]."'");
			return true;} else {
				unset($_SESSION[$this->cookies_pre."x_users_ip"]);
				unset($_SESSION[$this->cookies_pre."x_users_key"]);
				unset($_SESSION[$this->cookies_pre."x_users_id"]);					
				return false;}}						
			// Session: Check if Key Exists
			private function session_exists($key) {
			$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_sessions." WHERE key_type = '".$this->key_session."' AND session_key = '".$key."'");
			if(mysqli_fetch_array($r)){return true;} else {return false;}}		
			// Session: Create with unique key
			private function session_create($key, $userid) {
			if(!$this->multi_login) { if(!$this->log_sessions) { $this->mysqlcon->query("DELETE FROM ".$this->table_sessions." WHERE key_type = '".$this->key_session."' AND user_id = '".$userid."'");
			} else { $this->mysqlcon->query("UPDATE ".$this->table_sessions." SET is_active = 0 WHERE key_type = '".$this->key_session."' AND user_id = '".$userid."'");}} 
			if($this->save_ip_in_db) {$thenewip = @$_SERVER["REMOTE_ADDR"];} else {$thenewip = "hidden";}
			$this->mysqlcon->query("INSERT INTO ".$this->table_sessions."(user_id, key_type, session_key, is_active, request_ip, use_date)
			VALUES('".$userid."', '".$this->key_session."', '".$key."', '1', '".$thenewip."', CURRENT_TIMESTAMP())");}	
			// Session: Delete If Logged Out or with ID
			private function session_delete_at_logout() {
			if($this->multi_login) { $ext = "AND session_key = '".$this->user_session."' "; } else {$ext = " ";}
			if(!$this->log_sessions) {$this->mysqlcon->query("DELETE FROM ".$this->table_sessions." WHERE key_type = '".$this->key_session."' AND user_id = '".$this->user_id."' ".$ext);
			} else { $this->mysqlcon->query("UPDATE ".$this->table_sessions." SET is_active = 0 WHERE key_type = '".$this->key_session."' AND user_id = '".$this->user_id."' AND is_active = 1 ".$ext); } }
			// Session: Check if Valid
			private function session_valid($key, $userid) {
			$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_sessions." WHERE is_active = '1' AND session_key = '".mysqli_real_escape_string($this->mysqlcon, $key)."' AND key_type = '".$this->key_session."' AND user_id = '".mysqli_real_escape_string($this->mysqlcon, $userid)."'");
			if(mysqli_fetch_array($r)){ return true;} else {return false;}}	
	
		// Constructor of the Control Class
			function __construct($mysqlcon, $table_users, $table_sessions, $preecokie = "x_users_") {
				// Init Variables for Runtime
				$this->cookies_pre 		=   $preecokie;	
				$this->mysqlcon			=	$mysqlcon;
				$this->table_users		=	$table_users;
				$this->table_sessions	=	$table_sessions;
				// Start Session if not Exists and Ban Var Initialize if Not Initialized
				if (session_status() !== PHP_SESSION_ACTIVE) { @session_start(); }
			}	

		// Initialize Login after Configs have been made
			public function init() {
				// Restore Session If there Is a Login
				if(@$_SESSION[$this->cookies_pre."x_users_ip"] == @$_SERVER["REMOTE_ADDR"]
					AND isset($_SESSION[$this->cookies_pre."x_users_key"])
					AND isset($_SESSION[$this->cookies_pre."x_users_id"])) {
						if(!$this->session_valid($_SESSION[$this->cookies_pre."x_users_key"], $_SESSION[$this->cookies_pre."x_users_id"])) {
							unset($_SESSION[$this->cookies_pre."x_users_key"]);
							unset($_SESSION[$this->cookies_pre."x_users_ip"]);
							unset($_SESSION[$this->cookies_pre."x_users_id"]);
							unset($_SESSION[$this->cookies_pre."x_users_stay"]);
							$this->cookie_restore_session();} 
						else {  $this->session_restore();}
				} else {
					unset($_SESSION[$this->cookies_pre."x_users_key"]);
					unset($_SESSION[$this->cookies_pre."x_users_id"]);
					unset($_SESSION[$this->cookies_pre."x_users_ip"]);
					unset($_SESSION[$this->cookies_pre."x_users_stay"]);
					$this->cookie_restore_session();}				
			}

		// Logout a User on Request
			public function logout() {
				// Destroy Session, Cookies and Failure Counter
				@$this->session_delete_at_logout();
				@$this->cookie_delete();
				@$this->resetFailure();
				// Reset Session Vars
				$_SESSION[$this->cookies_pre."x_users_ip"] = false;
				$_SESSION[$this->cookies_pre."x_users_key"] = false;
				$_SESSION[$this->cookies_pre."x_users_id"]  = false;
				$_SESSION[$this->cookies_pre."x_users_stay"]  = false;
				unset($_SESSION[$this->cookies_pre."x_users_ip"]);
				unset($_SESSION[$this->cookies_pre."x_users_key"]);
				unset($_SESSION[$this->cookies_pre."x_users_id"]);
				unset($_SESSION[$this->cookies_pre."x_users_stay"]);
			}
			
		// Login Request to Get a User Logged in with REF and PASSWORD
			// Request Codes: 6 - Blocked by SessioN Ban
			// Request Codes: 5 - User is not yet confirmed
			// Request Codes: 4 - User is Disabled / Blocked
			// Request Codes: 3 - Wrong Password
			// Request Codes: 2 - User-Ref not Existant
			// Request Codes: 1 - Login OK
			public function login_request($ref, $password, $stayLoggedIn = false) {
				// Drop if Session Ban Banned
				if($this->isBanned()) {$this->login_request_code = 6; return false; } 
				$r	=	mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_users." WHERE LOWER(".$this->relevant_reference_username.") = \"".mysqli_real_escape_string($this->mysqlcon, strtolower(trim($ref)))."\"");
				if( $f = mysqli_fetch_array($r) ) {					
					if ( (strtolower($f[$this->relevant_reference_username]) == strtolower($ref) AND $this->passCheck($password, $f["user_pass"]))
						) {
						// Drop if User is Blocked
						if($f["is_blocked"] == 1) { $this->login_request_code = 4; return false; } 
						
						// Drop if Non-Confirmed
						if($f["is_confirmed"] != 1) { $this->login_request_code = 5; return false; } 
						
						// Reset Session Failures at OK Login
						$this->resetFailure(); 
						
						// Generate New Session Key
						$gennewkey	=	$this->genKey(32);
						while($this->session_exists($gennewkey)) {$gennewkey =	$this->genKey(32);}
						
						// Create Session and Cookies
						$this->session_create($gennewkey, $f["id"]);
						if($stayLoggedIn) { $this->cookie_add($f["id"], $gennewkey); }
						
						// Set up Variables
						 $this->user_id 			= $f["id"];
						 $this->user_session		= $gennewkey;		
						 $this->user_rank		    = $f["user_rank"];		
						 $this->user    		    = $f;		
						 $this->user_name		    = $f["user_name"];		
						 $this->user_loggedIn	    = true;						
						 $this->loggedIn	    = true;						
						 $this->user_mail			= $f["user_mail"];		
						 
						 // Update Last Login
						 $this->mysqlcon->query("UPDATE ".$this->table_users." SET last_login = CURRENT_TIMESTAMP() WHERE id = '".$f["id"]."'");
						 
						 // Reset or Disable old Reset Tokens
						 if($this->reset_token_login) {
							 if(!$this->log_recovers) {
							 $this->mysqlcon->query("DELETE FROM ".$this->table_sessions." WHERE user_id = '".$f["id"]."' AND key_type = '".$this->key_recover."'");
							 } else {
							 $this->mysqlcon->query("UPDATE ".$this->table_sessions." SET is_active = 0 WHERE user_id = '".$f["id"]."' AND key_type = '".$this->key_recover."'");
							 }
						 }

						// Set up Session Variables
						 $_SESSION[$this->cookies_pre."x_users_ip"]  = @$_SERVER["REMOTE_ADDR"];
						 $_SESSION[$this->cookies_pre."x_users_key"] = $gennewkey;
						 $_SESSION[$this->cookies_pre."x_users_id"]  = $f["id"];
						if($stayLoggedIn) { $_SESSION[$this->cookies_pre."x_users_stay"]  = true; } else { $_SESSION[$this->cookies_pre."x_users_stay"] = false; }						 
						 
						 // Request Code 1 is for Successfull Login
						 $this->login_request_code = 1;
						return true;
					} else { 
						// Request Code 3 is for Wrong Pass Login
						$this->raiseFailure(); 
						$this->login_request_code = 3; 
						return false; 
					}									
				} 
				// Request Code 2 is for User-Ref non Existant
				$this->login_request_code = 2; 
				return false;
			}


		// Functions to Recover Pass or Activate Account
			// Create new Req or Activation Token
			private function rec_token_create($userid, $key) {
				if(!$this->log_recovers) {$this->mysqlcon->query("DELETE FROM ".$this->table_sessions." WHERE key_type = '".$this->key_recover."' AND user_id = '".mysqli_real_escape_string($this->mysqlcon, $userid)."' ");	} else {
				$this->mysqlcon->query("UPDATE ".$this->table_sessions." SET is_active = 0 WHERE key_type = '".$this->key_recover."' AND user_id = '".mysqli_real_escape_string($this->mysqlcon, $userid)."' AND is_active = 1 ");}				
				if($this->save_ip_in_db) {$thenewip = @$_SERVER["REMOTE_ADDR"];} else {$thenewip = "hidden";}
				$this->mysqlcon->query("INSERT INTO ".$this->table_sessions."(user_id, key_type, session_key, is_active, request_ip)
				VALUES('".mysqli_real_escape_string($this->mysqlcon, $userid)."', '".$this->key_recover."', '".$key."', '1', '".$thenewip."')");}				
			// Check if a Rec or Activation Token is Valid
			public function rec_token_check($key, $userid) {
				$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_sessions." WHERE is_active = '1' AND session_key = '".mysqli_real_escape_string($this->mysqlcon, $key)."' AND key_type = '".$this->key_recover."' AND user_id = '".mysqli_real_escape_string($this->mysqlcon, $userid)."'");
				if(mysqli_num_rows($r)!=0){return true;} else {return false;}}			
			// Reset Request Preparation for Admins
			public function rec_request_by_id($ref) {
				$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_users."  WHERE id = \"".mysqli_real_escape_string($this->mysqlcon, $ref)."\"");
				while($f=mysqli_fetch_array($r)){
					// New Token for Reset
					$new_token	=	$this->genKey(10, "123456789");
					$this->resetFailure();
					$this->rec_token_create($f["id"], $new_token);
					
					// Request Code 1 is OK
					$this->rec_request_code			= 1;
					
					// Related Variables
					$this->mail_ref_token 			= $new_token;
					$this->mail_ref_user 			= $f["id"];
					$this->mail_ref_receiver		= $f["user_mail"];
					$this->mail_ref_receiver_name	= $f["user_name"];
					
					return true;
				} 
				// Code 2 is Failed - User not Existant
				$this->raiseFailure(); 
				$this->rec_request_code	=	2; 
				return false; 
			}							 
			// Reset Pass Request for User Ref (username or mail)		 
			public function rec_request($ref) {
				// Drop if Session Banned Code 4
				if($this->isBanned()) { $this->rec_request_code = 4; return false; } 
				$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_users."  WHERE LOWER(".$this->relevant_reference_username.") = \"".mysqli_real_escape_string($this->mysqlcon, strtolower(trim($ref)))."\"");
				while($f=mysqli_fetch_array($r)){
					// Check if Expired Code 3
					if(is_numeric($this->recover_intervall_hours)) {if(isset($f["reset_date"])) {if (strtotime($f["reset_date"]) > strtotime('-'.$this->recover_intervall_hours.' hours')) {$this->rec_request_code	=	3;return false;}}}
					// Tokens
					$new_token	=	$this->genKey(10, "123456789");
					$this->resetFailure();
					$this->rec_token_create($f["id"], $new_token);
					// Code 1 is OK
					$this->rec_request_code			= 1;
					// Related Vars
					$this->mail_ref_token 			= $new_token;
					$this->mail_ref_user 			= $f["id"];
					$this->mail_ref_receiver		= $f["user_mail"];
					$this->mail_ref_receiver_name	= $f["user_name"];
					return true;
				} 
				// Code 2 is User Ref not Found
				$this->raiseFailure(); 
				$this->rec_request_code	=	2; 
				return false; 
			}							 
			// Confirm Password Request with Token
			public function rec_confirm($userid, $token, $newpass) {
				if($this->isBanned()) { $this->rec_request_code = 4; return false; }
				if(!is_numeric($userid)) { $this->rec_request_code = 2; return false; }
				$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_sessions." WHERE session_key = '".mysqli_real_escape_string($this->mysqlcon, trim($token))."' AND key_type = '".$this->key_recover."' AND user_id = '".$userid."' AND is_active = 1");
				if($f= mysqli_fetch_array($r)){
					// Delete all the old Recover Codes if Confirmation complete
					if($this->log_recovers) {$this->mysqlcon->query("UPDATE ".$this->table_sessions." SET is_active = 0 WHERE user_id = ".$f["user_id"]." AND key_type = '".$this->key_recover."'");	
					} else { $this->mysqlcon->query("DELETE FROM ".$this->table_sessions." WHERE user_id = ".$f["user_id"]." AND key_type = '".$this->key_recover."'");}			

					// Check if Expired
						if(is_numeric($this->recover_token_lifetime_hours)) {if (strtotime($f["create_date"]) < strtotime('-'.$this->recover_token_lifetime_hours.' hours')) {$this->rec_confirm_code	=	3;return false;}}
					
					// Update Reset Date if First Activation
						$this->mysqlcon->query("UPDATE ".$this->table_users." SET activation_date = CURRENT_TIMESTAMP() WHERE id = '".$userid."' AND activation_date IS NULL");
						$this->mysqlcon->query("UPDATE ".$this->table_users." SET reset_date = CURRENT_TIMESTAMP() WHERE id = '".$userid."'");
						$this->mysqlcon->query("UPDATE ".$this->table_users." SET is_confirmed = 1 WHERE id = '".$userid."'");
					// Update to new Password
						$this->changeUserPass($f["user_id"], $newpass);
						$this->rec_confirm_code = 1;
					return true;
				} else {
					$this->rec_confirm_code = 2; 
					$this->raiseFailure(); 
					return false;
				}
			}						 
			// Confirm Password Request with Token but not set Password
			public function rec_confirm_activate($userid, $token) {
				if($this->isBanned()) { $this->rec_request_code = 4; return false; }
				if(!is_numeric($userid)) { $this->rec_request_code = 2; return false; }
				$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_sessions." WHERE session_key = '".mysqli_real_escape_string($this->mysqlcon, trim($token))."' AND key_type = '".$this->key_recover."' AND user_id = '".$userid."' AND is_active = 1");
				if($f= mysqli_fetch_array($r)){
					// Delete all the old Recover Codes if Confirmation complete
					if($this->log_recovers) {$this->mysqlcon->query("UPDATE ".$this->table_sessions." SET is_active = 0 WHERE user_id = ".$f["user_id"]." AND key_type = '".$this->key_recover."'");	
					} else { $this->mysqlcon->query("DELETE FROM ".$this->table_sessions." WHERE user_id = ".$f["user_id"]." AND key_type = '".$this->key_recover."'");}			

					// Check if Expired
						if(is_numeric($this->recover_token_lifetime_hours)) {if (strtotime($f["create_date"]) < strtotime('-'.$this->recover_token_lifetime_hours.' hours')) {$this->rec_confirm_code	=	3;return false;}}
					
					// Update Reset Date if First Activation
						$this->mysqlcon->query("UPDATE ".$this->table_users." SET activation_date = CURRENT_TIMESTAMP() WHERE id = '".$userid."'");
						$this->mysqlcon->query("UPDATE ".$this->table_users." SET reset_date = CURRENT_TIMESTAMP() WHERE id = '".$userid."'");
						$this->mysqlcon->query("UPDATE ".$this->table_users." SET is_confirmed = 1 WHERE id = '".$userid."'");
					// Update to new Password
						$this->rec_confirm_code = 1;
					return true;
				} else {
					$this->rec_confirm_code = 2; 
					$this->raiseFailure(); 
					return false;
				}
			}
			
		// Functions to Register new "Shadow" Mail
			// Create Mail Change Confirmation Token
			private function cmwc_token_create($userid, $key) {
				if(!$this->log_user_mailchange) {$this->mysqlcon->query("DELETE FROM ".$this->table_sessions." WHERE key_type = '".$this->key_mail_change."' AND user_id = '".mysqli_real_escape_string($this->mysqlcon, $userid)."' ");	} else {
				$this->mysqlcon->query("UPDATE ".$this->table_sessions." SET is_active = 0 WHERE key_type = '".$this->key_mail_change."' AND user_id = '".mysqli_real_escape_string($this->mysqlcon, $userid)."' AND is_active = 1 ");}				
				if($this->save_ip_in_db) {$thenewip = @$_SERVER["REMOTE_ADDR"];} else {$thenewip = "hidden";}
				$this->mysqlcon->query("INSERT INTO ".$this->table_sessions."(user_id, key_type, session_key, is_active, request_ip)
				VALUES('".mysqli_real_escape_string($this->mysqlcon, $userid)."', '".$this->key_mail_change."', '".$key."', '1', '".$thenewip."')");}			
			// User Request Mail Change and Confirm
			public function change_mail_with_confirmation($id, $newmail, $nointervall = false) {
				if(!is_numeric($id)) { $this->user_mailchange_confirm_code = 2; return false; }				
				
				if($this->relevant_reference_username != "user_mail") { 
					// Do nothign multiple is allowed
				} else {
					$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_users."  WHERE user_mail LIKE \"".mysqli_real_escape_string($this->mysqlcon, strtolower(trim($newmail)))."\" AND is_confirmed = 1");
					if($f=mysqli_fetch_array($r)){	$this->user_mailchange_confirm_code = 3; return false; } // Existant already 					
				}
				
				$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_users."  WHERE id = \"".mysqli_real_escape_string($this->mysqlcon, $id)."\"");
				if($f=mysqli_fetch_array($r)){
					
					// Check if Expired Code 3
					if(!$nointervall) { if(is_numeric($this->mailchange_intervall_hours)) {if(isset($f["mail_change_date"])) {if (strtotime($f["mail_change_date"]) > strtotime('-'.$this->mailchange_intervall_hours.' hours')) {$this->user_mailchange_confirm_code	=	4;return false;}}} }
					
					// Change Shadow Mail
					$this->changeUserShadowMail($id, $newmail);
					
					// Key Handling
					if($this->log_user_mailchange) {$this->mysqlcon->query("UPDATE ".$this->table_sessions." SET is_active = 0 WHERE user_id = ".$f["id"]." AND key_type = '".$this->key_mail_change."'");	
					} else { $this->mysqlcon->query("DELETE FROM ".$this->table_sessions." WHERE user_id = ".$f["id"]." AND key_type = '".$this->key_mail_change."'");};
					
					// Confirm Code 1 - OK
					$this->user_mailchange_confirm_code = 1;
					
					// Tokens
					$new_token	=	$this->genKey(10, "123456789");
					$this->cmwc_token_create($f["id"], $new_token);
					
					// Vars for Mail
					$this->mail_ref_token 			= $new_token;
					$this->mail_ref_user 			= $f["id"];
					$this->mail_ref_receiver		= $newmail;
					$this->mail_ref_receiver_name	= $f["user_name"];
					
					return true;
				} 
				// Confirm Code 2 - ID not Found
				$this->user_mailchange_confirm_code = 2;
				return false; 
			}
			// Confirm Change of Mail Address
			public function change_mail_with_confirmation_execute($userid, $token) {
				if(!is_numeric($userid)) { $this->user_mailchange_confirm_code = 2; return false; }
				if($this->isBanned()) { $this->user_mailchange_confirm_code = 4; return false; } 
				
			$r = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_sessions." WHERE session_key = '".mysqli_real_escape_string($this->mysqlcon, $token)."' AND key_type = '".$this->key_mail_change."' AND user_id = '".mysqli_real_escape_string($this->mysqlcon, $userid)."' AND is_active = 1");
			if($f= mysqli_fetch_array($r)){
				// Delete all the old Recover Codes if Confirmation complete
				if($this->log_user_mailchange) {$this->mysqlcon->query("UPDATE ".$this->table_sessions." SET is_active = 0 WHERE user_id = ".$f["user_id"]." AND key_type = '".$this->key_mail_change."'");	
				} else { $this->mysqlcon->query("DELETE FROM ".$this->table_sessions." WHERE user_id = ".$f["user_id"]." AND key_type = '".$this->key_mail_change."'");}			

				// Check if Expired
					if(is_numeric($this->usermailchange_token_lifetime_hours)) { if (strtotime($f["create_date"]) < strtotime('-'.$this->usermailchange_token_lifetime_hours.' hours')) {$this->user_mailchange_confirm_code	=	3;return false;} }

				
				$x = mysqli_query($this->mysqlcon, "SELECT * FROM ".$this->table_users."  WHERE id = \"".mysqli_real_escape_string($this->mysqlcon, $userid)."\"");
				if($xf=mysqli_fetch_array($x)) {
						if($xf["user_mail_shadow"] == NULL OR trim($xf["user_mail_shadow"]) == "") { $this->user_mailchange_confirm_code = 5; return false; }
						if($this->relevant_reference_username != "user_mail") { 
							$this->mysqlcon->query("UPDATE ".$this->table_users." SET mail_change_date = CURRENT_TIMESTAMP() WHERE id = '".$f["id"]."'");
							$this->changeUserMail($f["user_id"], $xf["user_mail_shadow"]);
							$this->mysqlcon->query("UPDATE ".$this->table_users." SET user_mail_shadow = NULL WHERE id = '".mysqli_real_escape_string($this->mysqlcon, $userid)."'");
						} else {
							$this->mysqlcon->query("UPDATE ".$this->table_users." SET mail_change_date = CURRENT_TIMESTAMP() WHERE id = '".$f["id"]."'");
							$this->mysqlcon->query("UPDATE ".$this->table_users." SET user_mail_shadow = NULL WHERE user_mail_shadow = '".mysqli_real_escape_string($this->mysqlcon, strtolower(trim($xf["user_mail_shadow"])))."'");					
						}
					}
					$this->user_mailchange_confirm_code = 1;
				return true;
			} else {$this->user_mailchange_confirm_code = 2; $this->raiseFailure(); return false;}}
	}
?>