<?php
	/*	__________ ____ ___  ___________________.___  _________ ___ ___  
		\______   \    |   \/  _____/\_   _____/|   |/   _____//   |   \ 
		 |    |  _/    |   /   \  ___ |    __)  |   |\_____  \/    ~    \
		 |    |   \    |  /\    \_\  \|     \   |   |/        \    Y    /
		 |______  /______/  \______  /\___  /   |___/_______  /\___|_  / 
				\/                 \/     \/                \/       \/  MySQL Control Class */
	
	class x_class_mysql {
		/*	__   ____ _ _ __ ___ 
			\ \ / / _` | '__/ __|
			 \ V / (_| | |  \__ \
			  \_/ \__,_|_|  |___/*/
			public  $mysqlcon			  = false;
			private $transaction 		  = false;
			public  $lasterror			  = false;
			public  $insert_id			  = false;
			private $logging			  = false;
			private $logging_table		  = "";
			private $logging_section	  = "";
				public function  loggingSetup($bool, $table, $section = "") {$this->logging = $bool; $this->logging_table = $table; $this->logging_section = $section;}
				private function logError($string) {if($this->mysqlcon AND $this->logging) {$inarray["section"] = $this->logging_section;$inarray["url"] = $this->escape(@$_SERVER["REQUEST_URI"]);$inarray["errtext"] = $this->escape(@$string);$this->insert($this->logging_table, $inarray);}}

		/*									 _   _             
											| | (_)            
			  ___ ___  _ __  _ __   ___  ___| |_ _  ___  _ __  
			 / __/ _ \| '_ \| '_ \ / _ \/ __| __| |/ _ \| '_ \ 
			| (_| (_) | | | | | | |  __/ (__| |_| | (_) | | | |
			 \___\___/|_| |_|_| |_|\___|\___|\__|_|\___/|_| |_|*/ 
			/********************** Construct Connection ****/	 
			function __construct($hostname, $username, $password, $database, $ovrcon = false) {if(!$ovrcon) {$this->mysqlcon = @mysqli_connect($hostname, $username, $password, $database);
			if(@mysqli_connect_errno()) {$this->lasterror  =  @mysqli_connect_error();}} else {$this->mysqlcon = $ovrcon; $this->ping();}}
			/********************** Destroy Connection ****/	
			function __destruct() 			{if($this->mysqlcon) {/*@mysqli_close($this->mysqlcon);*/}}
			/********************** Get Connection Status ****/	
			public function status() 		{if($this->mysqlcon) {$this->lasterror = false;return true;} else {$this->lasterror = "No MySQL Connection Started";return false;}}
			/********************** Get Ping MySQL Status ****/	
			public function ping() 			{if($this->mysqlcon) {return $this->logLastError(@mysqli_ping($this->mysqlcon), "ping");} else {$this->lasterror = "No MySQL Connection Started"; return false;}}
			/********************** Log Connection Error in Var ****/	
			private function logLastError($errorBool, $lq = "") {if(!@$errorBool) {$this->lasterror = @mysqli_error($this->mysqlcon);$this->logError($this->lasterror." - At Query - ".$lq);} else {$this->insert_id = @$this->mysqlcon->insert_id;$this->lasterror = false;} return $errorBool;}
			/********************** Log Connection Error in Var ****/	
			public function lastError() 	{return $this->lasterror;}
			/********************** Mysql Filter a Variable ****/	
			public function escape($val) 	{if($this->mysqlcon) {return @mysqli_real_escape_string($this->mysqlcon, $val);} else {return false;}}	
		
		/*			  _           _   
					 | |         | |  
			 ___  ___| | ___  ___| |_ 
			/ __|/ _ \ |/ _ \/ __| __|
			\__ \  __/ |  __/ (__| |_ 
			|___/\___|_|\___|\___|\__|*/
			/********************** Select Statement for Multi Purpose ****/	 
			/********************** Multiple = True get Multi Array with different Rows | False get Single Array with Row ****/	 
			/********************** If Bindarray = false -> normal input query | If not deliver Multi Array with Array[X]["value"] = VALUE and Array[X]["type"] ****/	 
			/********************** TYPE //Binding parameters. Types: s = string, i = integer, d = double,  b = blob] ****/	 
			public function select($query, $multiple = false, $bindarray = false){
				if(is_array($bindarray)) {
					if ($this->logLastError($stmt = @$this->mysqlcon->prepare($query), $query." At Preparation")) { 
						$before_prepare	=	"";
						$params	=	array(); 
						foreach ($bindarray as $key => $value) {
							$before_prepare .= $value["type"];
							array_push($params, $value["value"]);};
						array_unshift($params, $before_prepare);
						$tmp = array();
						foreach($params as $key => $value) {$tmp[$key] = &$params[$key];}
						@call_user_func_array(array($stmt, 'bind_param'), $tmp);
						if($this->logLastError(@$stmt->execute(), $query." At Execution")){
							if($newres = $this->logLastError(@$stmt->get_result())){
								if($multiple) {
									$row = array();
									while($rowx = $newres->fetch_array(MYSQLI_ASSOC)) {
									  array_push($row, $rowx);}							
									return $row;
								} else {return @$newres->fetch_array(MYSQLI_ASSOC);}
					} else { return false;}} else {return false;}} else {return false;}
				} else {
					if ($sql_res = $this->logLastError(@mysqli_query($this->mysqlcon, $query), $query)) { if (mysqli_num_rows($sql_res) > 0) {
						if(!$multiple) {return mysqli_fetch_array($sql_res, MYSQLI_ASSOC); }
						$count = mysqli_num_rows($sql_res);$row = array();for ($i=0; $i<$count; $i++){
						$tmpnow = mysqli_fetch_array($sql_res, MYSQLI_ASSOC); $row[$i] = $tmpnow;}return $row;				
					} else {return false;}} else {return false;}}
				}

		/*	 _                     _   
			(_)                   | |  
			 _ _ __  ___  ___ _ __| |_ 
			| | '_ \/ __|/ _ \ '__| __|
			| | | | \__ \  __/ |  | |_ 
			|_|_| |_|___/\___|_|   \__|*/
			/********************** Select Statement for Multi Purpose ****/	 
			/********************** Table = Table Name to Insert ****/	 
			/********************** Array = Array to be Inserted Array[fieldname] = value ****/	 
			/********************** If Bindarray = false -> normal input query | If not deliver Multi Array with Array[X]["value"] = VALUE and Array[X]["type"] ****/	 
			/********************** TYPE //Binding parameters. Types: s = string, i = integer, d = double,  b = blob] ****/	 
			public function insert($table, $array, $bindarray = false){
				if(is_array($bindarray)) {
					if(!is_array($array)) {return false;}
					$build_first	=	"";$build_second	=	"";$firstrun = true;
					foreach( $array as $key => $value ){if(!$firstrun) {$build_first .= ", ";}
					if(!$firstrun) {$build_second .= ", ";}$build_first .= $key;
					if($value == "?") {$build_second .= $value;} else {$build_second .= "'".$value."'";}$firstrun = false;}
					$newquery = 'INSERT INTO '.$table.'('.$build_first.') VALUES('.$build_second.');';
					if ($this->logLastError($stmt = @$this->mysqlcon->prepare($newquery), $newquery." At Preparation")) { 
						$before_prepare	=	"";
						$params	=	array(); 
						foreach ($bindarray as $key => $value) {
							$before_prepare .= $value["type"];
							array_push($params, $value["value"]);};
						array_unshift($params, $before_prepare);
						$tmp = array();
						foreach($params as $key => $value) {$tmp[$key] = &$params[$key];}
						@call_user_func_array(array($stmt, 'bind_param'), $tmp);
						return $this->logLastError(@$stmt->execute(), $newquery." At Execution"); } else {return false;}
				} else {	
					if(!is_array($array)) {return false;}
					$build_first	=	"";$build_second	=	"";$firstrun = true;
					foreach( $array as $key => $value ){if(!$firstrun) {$build_first .= ", ";}
					if(!$firstrun) {$build_second .= ", ";}$build_first .= $key;
					$build_second .= "'".$value."'";
					$firstrun = false;}
					$nnnnquery	=	'INSERT INTO '.$table.'('.$build_first.') VALUES('.$this->escape($build_second).');';
					return $this->logLastError(@mysqli_query($this->mysqlcon, $nnnnquery), $nnnnquery);					
				}
			}
				
		/*	  __ _ _   _  ___ _ __ _   _ 
			 / _` | | | |/ _ \ '__| | | |
			| (_| | |_| |  __/ |  | |_| |
			 \__, |\__,_|\___|_|   \__, |
				| |                 __/ |
				|_|                |___/ */
			/********************** Select Statement for Multi Purpose ****/	 
			/********************** Query = The Query ****/	 
			/********************** If Bindarray = false -> normal input query | If not deliver Multi Array with Array[X]["value"] = VALUE and Array[X]["type"] ****/	 
			/********************** TYPE //Binding parameters. Types: s = string, i = integer, d = double,  b = blob] ****/	 
			public function query($query, $bindarray = false){
				if(is_array($bindarray)) {
					if ($this->logLastError($stmt = @$this->mysqlcon->prepare($query), $query." At Preparation")) { 
						$before_prepare	=	"";
						$params	=	array(); 
						foreach ($bindarray as $key => $value) {
							$before_prepare .= $value["type"];
							array_push($params, $value["value"]);};
						array_unshift($params, $before_prepare);
						$tmp = array();
						foreach($params as $key => $value) {$tmp[$key] = &$params[$key];}
						@call_user_func_array(array($stmt, 'bind_param'), $tmp);
						return $this->logLastError(@$stmt->execute(), $query." At Execution");
						} else {return false;}
			} else { return $this->logLastError(@mysqli_query($this->mysqlcon, $query), $query);}}
				
		/*			   _          
					  (_)         
			 _ __ ___  _ ___  ___ 
			| '_ ` _ \| / __|/ __|
			| | | | | | \__ \ (__ 
			|_| |_| |_|_|___/\___|*/
			public function increase($table, $nameidfield, $id, $increasefield, $increasevalue = 1){
				$newquery = "UPDATE ".$table." SET ".$increasefield." = ".$increasefield." + ".$increasevalue." WHERE ".$nameidfield." = '".$id."'";
				return $this->logLastError(@mysqli_query($this->mysqlcon, $newquery), $newquery);}
			
			public function decrease($table, $nameidfield, $id, $decreasefield, $decreasevalue = 1){
				$newquery = "UPDATE ".$table." SET ".$decreasefield." = ".$decreasefield." + ".$decreasevalue." WHERE ".$nameidfield." = '".$id."'";
				return $this->logLastError(@mysqli_query($this->mysqlcon, $newquery), $newquery);}									

			public function displayError($exit = false) {
				http_response_code(503);
				echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
				"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
				<html version="-//W3C//DTD XHTML 1.1//EN"
					  xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"
					  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
					  xsi:schemaLocation="http://www.w3.org/1999/xhtml
										  http://www.w3.org/MarkUp/SCHEMA/xhtml11.xsd">
					<head>
						<title>Database Error</title>
						<meta http-equiv="content-Type" content="text/html; utf-8" />
						<meta name="robots" content="noindex, nofollow" />
						<meta http-equiv="Pragma" content="no-cache" />
						<meta http-equiv="content-Language" content="en" />
						<meta name="viewport" content="width=device-width, initial-scale=1">
						<style>
						html, body { background: blue; color: white; font-family: Arial; text-align: center; margin: 0 0 0 0; padding: 0 0 0 0; position: absolute; width: 100%; top: 0px; left: 0px; height: 100vh; }
						a { color: black; text-decoration: none; font-weight: bold; background: green; border-radius: 10px; font-size: 16px; padding: 15px; word-break: keep-all; white-space: nowrap; }		
						a:hover { color: black; text-decoration: none; font-weight: bold; background: white; border-radius: 10px; font-size: 16px; padding: 15px; }
						#dberrorwrapper { text-align: center; color: lightblue; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); }
						</style>
						<meta name="expires" content="0" />	
					</head>
					<body>
						<div id="dberrorwrapper"><font size="+5">Error 503</font><br/><font size="+3">Site under Maintenance...</font><br />Please check in later! x)</div>
					</body></html>';if($exit){exit();}}

		/*	 _                                  _   _                 
			| |                                | | (_)                
			| |_ _ __ __ _ _ __  ___  __ _  ___| |_ _  ___  _ __  ___ 
			| __| '__/ _` | '_ \/ __|/ _` |/ __| __| |/ _ \| '_ \/ __|
			| |_| | | (_| | | | \__ \ (_| | (__| |_| | (_) | | | \__ \
			 \__|_|  \__,_|_| |_|___/\__,_|\___|\__|_|\___/|_| |_|___/*/
			/********************** Start A Transaction if none is Running ****/
			public function transaction() {if($this->mysqlcon && !$this->transaction) { $this->transaction = true; return $this->logLastError(@mysqli_begin_transaction($this->mysqlcon), "Begin Transaction");}$this->lasterror = "Another Transaction Running or Mysql Connection Error"; return false;}
			/********************** Rollback A Transaction if is Running ****/
			public function rollback() {if($this->mysqlcon && $this->transaction) { $this->transaction = false; return $this->logLastError(@mysqli_rollback($this->mysqlcon), "rollback");}$this->lasterror = "No Transaction Started or Mysql Connection Error";return false;}
			/********************** Get Transaction Status ****/
			public function transactionStatus() {return $this->transaction;}
			/********************** Commit a Transaction ****/
			public function commit() {if($this->mysqlcon && $this->transaction) { $this->transaction = false; return $this->logLastError(@mysqli_commit($this->mysqlcon), "Commit");}$this->lasterror = "No Transaction Started or Mysql Connection Error";return false;}
	}
?>