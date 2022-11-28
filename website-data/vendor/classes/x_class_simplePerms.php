<?php
	/*	__________ ____ ___  ___________________.___  _________ ___ ___  
		\______   \    |   \/  _____/\_   _____/|   |/   _____//   |   \ 
		 |    |  _/    |   /   \  ___ |    __)  |   |\_____  \/    ~    \
		 |    |   \    |  /\    \_\  \|     \   |   |/        \    Y    /
		 |______  /______/  \______  /\___  /   |___/_______  /\___|_  / 
				\/                 \/     \/                \/       \/  Simple Perms Control Class */	
				
	class x_class_simplePerms {
		private $mysql = false;
		private $tablename = false;
		private $section = "undefined";
		
		function __construct($mysql, $tablename, $section = "undefined") {
			$this->mysql	= $mysql;
			$this->tablename = $tablename;
			$this->section = $section;
		}

		public function getPerm($ref) {
			if(is_numeric($ref)) { 
				$query = mysqli_query($this->mysql, "SELECT * FROM ".$this->tablename." WHERE ref = \"".$ref."\" AND section = '".$this->section."'");
				if ($result	=	mysqli_fetch_array($query) ) {
					$newar	= unserialize($result["content"]);					
					if(is_array($newar)) { return $newar; } else {return array();}
				} 
				return array();
			} return array();		
		}
		
		public function addPerm($ref, $permname) {
			$current_perm	=	$this->getPerm($ref);
			$hasperm = false;
			if(is_array($current_perm)) {
				foreach($current_perm AS $key => $value) {
					if($value == $permname) { $hasperm = true; }
				}
			} else { $current_perm = array(); }

			if(!$hasperm) {array_push($current_perm, $permname);}		
			$this->setPerm($ref, $current_perm);		
			return true;			
		}
		
		public function hasPerm($ref, $permname) {
			$current_perm	=	$this->getPerm($ref);
			if(is_array($current_perm)) {
				foreach($current_perm AS $key => $value) {
					if($value == $permname) { return true; }
				}
			}	
			return false;			
		}
		
		private function setPerm($ref, $array) {	
			if(is_numeric($ref)) { 
				$query = mysqli_query($this->mysql, "SELECT * FROM ".$this->tablename." WHERE ref = \"".$ref."\" AND section = '".$this->section."'");
				if ($result	=	mysqli_fetch_array($query) ) { 
					mysqli_query($this->mysql, "UPDATE ".$this->tablename." SET content = '".mysqli_real_escape_string($this->mysql, serialize($array))."' WHERE ref = '".$ref."' AND section = '".$this->section."'  ");
				} else { 
					mysqli_query($this->mysql, "INSERT INTO ".$this->tablename." (ref, content, section) VALUES('".$ref."', '".mysqli_real_escape_string($this->mysql, serialize($array))."', '".$this->section."')"); 
				}
				return true;
			} return false; 		
		}

		public function removePerm($ref, $permname) {
			$current_perm	=	$this->getPerm($ref);
			$newperm	=	array();
			if(is_array($current_perm)) {
				foreach($current_perm AS $key => $value) {
					if($value != $permname) { array_push($newperm, $value); }
				}
			}
			$this->setPerm($ref, $newperm);
			return true;
		}
		
		// Flush a REF From the Perms Table
		public function flush() {
			return mysqli_query($this->mysql, "DELETE FROM ".$this->tablename." WHERE ref = \"".$ref."\" AND section = '".$this->section."'");
		}
	}
?>