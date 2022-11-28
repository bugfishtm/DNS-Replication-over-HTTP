<?php
	/*	__________ ____ ___  ___________________.___  _________ ___ ___  
		\______   \    |   \/  _____/\_   _____/|   |/   _____//   |   \ 
		 |    |  _/    |   /   \  ___ |    __)  |   |\_____  \/    ~    \
		 |    |   \    |  /\    \_\  \|     \   |   |/        \    Y    /
		 |______  /______/  \______  /\___  /   |___/_______  /\___|_  / 
				\/                 \/     \/                \/       \/ X Function Set	*/

	//////////////////////////////////////////////
	// Check if Script is run in CLI
	//////////////////////////////////////////////
	function x_inCLI() {
		$sapi_type = php_sapi_name();
		if (substr($sapi_type, 0, 3) == 'cgi') { return true;
		} else { return false; }
	}

	//////////////////////////////////////////////
	// Replacement for HTMLSPECIALCHARS
	//////////////////////////////////////////////	
	function x_hsc($string) {
		return htmlspecialchars(@$string);
	}
	
	//////////////////////////////////////////////
	// Button Functions
	//////////////////////////////////////////////
		function x_executionButton($db, $name, $url, $query, $get, $msgerr = "Fehler!", $msgok = "Erfolgreich!", $break = false, $style = ""){
			if(strpos(trim($url), "?") > 2) { $xurl = trim($url)."&".$get."=x"; } else {$xurl = trim($url)."?".$get."=x";} print "<a href='".$xurl."' class='x_executionButton' style='".$style."'>".$name."</a>";if($break) {echo "<br />";} if(@$_GET[$get] == "x") { if($db->query($query)) { return true; } else {return false;}  $url = str_replace("?".$get."=x&", "?", $url); $url = str_replace("&".$get."=x", "", $url);  print '<meta http-equiv="refresh" content="0; url='.$url.'">';} return false;}	
		function x_button($name, $url, $break = false, $style = "", $reacttourl = true){  if($reacttourl AND strpos($url."&", $_SERVER["REQUEST_URI"]."&") > -1) {$style .= ";background: grey !important;";} print "<a href='".$url."' class='x_button' style='".$style."'>".$name."</a>"; if($break) {echo "<br />";}}
	
	//////////////////////////////////////////////
	// Redirect Functions
	//////////////////////////////////////////////
		function x_html_redirect($url, $seconds = 0) { echo '<meta http-equiv="refresh" content="'.$seconds.'; url='.$url.'">';}
		
	//////////////////////////////////////////////
	// Param and Check Functions
	//////////////////////////////////////////////
		function x_isset($val) {if(trim(@$val) != '' AND strlen(@$val) > 0 ) {return true;} else {return false;}} ## Check if a value is not null and strlen more than 1
		function x_datediff_before($d1, $d2, $length)  ## x_datediff_before($d1, $d2, $length) Check if d1 is difference as length with d2 
			{if($d1 == false OR $d2 == false) { return false; } {}$interval = @date_diff(@date_create($d1), @date_create($d2));if( @$interval->format('%a') > $length ) { return true;} return false;}
		function x_imgValid($url) {if(!isset($url)) {return false;}else {if(is_string(trim($url)) AND strlen($url) > 3) {return @getimagesize($url);} else {return false;}} }

	//////////////////////////////////////////////
	// Word Filter Functions
	//////////////////////////////////////////////
		function x_contains_cyrillic($val)  ## Check if a String contains cyrillic chars
			{$contains_cyrillic = (bool) preg_match('/[\p{Cyrillic}]/u', $val);if ($contains_cyrillic) { return true; } else {return false;}}
		function x_contains_bad_word($val) { ## Check if String Contains bad Words by Filter
				if(strpos($val, " porn ") !== false){ return false; }
				if(strpos($val, " Porn ") !== false){ return false; }
			  return true;}
		function x_contains_url($val) { ## Check if String Contains URL
				if(strpos($val, "http://") !== false){ return false; }
				if(strpos($val, "https://") !== false){ return false; }
			  return true;}
			  
	//////////////////////////////////////////////
	// Post and Get Parameter Functions
	//////////////////////////////////////////////
		function x_getint($val) { if(is_numeric(@$_GET[$val])) { return @$_GET[$val];} else { return false;}} ## Get a GET value if INT
		function x_postint($val) { if(is_numeric(@$_POST[$val])) { return @$_POST[$val];} else { return false;}} ## Get a POST value if INT
		function x_get($val) {if(isset($_GET[$val])) { return @$_GET[$val];} else { return false;}} ## Get a GET value
		function x_post($val) {if(isset($_POST[$val])) { return @$_POST[$val];} else { return false;}} ## Get a POST value
?>