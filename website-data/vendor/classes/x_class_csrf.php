<?php
	/*	__________ ____ ___  ___________________.___  _________ ___ ___  
		\______   \    |   \/  _____/\_   _____/|   |/   _____//   |   \ 
		 |    |  _/    |   /   \  ___ |    __)  |   |\_____  \/    ~    \
		 |    |   \    |  /\    \_\  \|     \   |   |/        \    Y    /
		 |______  /______/  \______  /\___  /   |___/_______  /\___|_  / 
				\/                 \/     \/                \/       \/  CSRF Class	*/	
	class x_class_csrf {
		// Class Variables Private
		private $c_extension 		= false;
		private $c_key		 		= false; 
			public function get() {return $this->c_key;} 
			public function getField($name, $id = "") {echo '<input type="hidden" name="'.$name.'" id="'.$id.'" value="'.$this->c_key.'">';}
		private $l_key		 		= false; 
			public function get_lkey() { return $this->l_key; }
		private $l_key_time	 		= false; 
			public function get_lkey_time() { return $this->l_key_time; }
		private $c_valid_time 		= false; 
			public function overrideValidTime($seconds_valid) { $this->c_valid_time = $seconds_valid; }
		private $c_disableRenewal 	= false; 
			public function disableRenewal($bool = true) {$this->c_disableRenewal = $bool;} 
			public function isDisabled() {return $this->c_disableRenewal;}		
			
		// Construct and Generate a Session Key
		function __construct($cookie_extension = "", $second_valid = 300, $disableRenew = false) {
			if (session_status() === PHP_SESSION_NONE) {session_start();}
			$this->c_extension  	 = $cookie_extension;
			$this->c_disableRenewal  = $disableRenew;
			$this->c_valid_time 	 = $second_valid;
			$this->c_key			 = mt_rand(100000000,900000000);
			$this->l_key			 = @$_SESSION[$this->c_extension."x_csrf"];
			$this->l_key_time		 = @$_SESSION[$this->c_extension."x_csrf_time"];
		}
			
		// Check if Submitted CSRF if Valid with CSRF Before in Session
		public function check($code, $override_valid_time = false) {
			if(!$override_valid_time) { $override_valid_time = $this->c_valid_time;}
			if(@$code == @$_SESSION[$this->c_extension."x_csrf"] AND @$code != NULL AND @trim($code) != "") {
				if((time() - @$_SESSION[$this->c_extension."x_csrf_time"]) < $override_valid_time 
					AND @$_SESSION[$this->c_extension."x_csrf_time"] != NULL AND isset($_SESSION[$this->c_extension."x_csrf_time"])) {
						return true;
				} return false;
			}
			return false;
		}

		public function check_lkey($code, $override_valid_time = false) {
			if(@$this->l_key_time == false) { 
				$cct = "undef"; 
			} else { $cct = @$this->l_key_time; }	
			if(!$override_valid_time) {
				$override_valid_time = $this->c_valid_time;
			}
			if(@$code == @$this->l_key AND @$code != NULL AND @trim($code) != "") {
				if((time() - $cct) < $override_valid_time AND $cct != NULL AND isset($cct)) {
					return true;
				}
			} 
			return false;
		} 
			
		// Deconstruct and Apply new CSRF to Session
		function __destruct() {
			if(!$this->c_disableRenewal) {
				$_SESSION[$this->c_extension."x_csrf"] = $this->c_key;
				$_SESSION[$this->c_extension."x_csrf_time"]  = time();
			}
		}			
	}
?>