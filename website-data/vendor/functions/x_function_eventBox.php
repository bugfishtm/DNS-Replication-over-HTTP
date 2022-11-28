<?php
	/*	__________ ____ ___  ___________________.___  _________ ___ ___  
		\______   \    |   \/  _____/\_   _____/|   |/   _____//   |   \ 
		 |    |  _/    |   /   \  ___ |    __)  |   |\_____  \/    ~    \
		 |    |   \    |  /\    \_\  \|     \   |   |/        \    Y    /
		 |______  /______/  \______  /\___  /   |___/_______  /\___|_  / 
				\/                 \/     \/                \/       \/  EventWindows */
	function x_eventBoxPrep($text, $type = "x", $precookie = "", $morecss = "", $buttontext = "X") {
		if($type == "ok"|| $type == "success") {$morecss = '<div id="x_eventBox_ok" class="x_eventBox" style="'.$morecss.'" >'.$text;}
		elseif($type == "warning" || $type == "warn" || $type == "warns") {$morecss = '<div id="x_eventBox_warning" class="x_eventBox" style="'.$morecss.'" >'.$text;}
		elseif($type == "error" || $type == "errors" || $type == "fail") {$morecss = '<div id="x_eventBox_error" class="x_eventBox" style="'.$morecss.'" >'.$text;}
		else {$morecss = '<div id="x_eventBox_'.$type.'" class="x_eventBox" style="'.$morecss.'" >'.$text;}				
		$morecss = $morecss."<button class='x_eventBoxButton' onclick='this.parentNode.remove()'>".$buttontext."</button></div>";
		$_SESSION[$precookie."x_eventbox"] = $morecss;}

	function x_eventBoxShow($precookie = "") { echo @$_SESSION[$precookie."x_eventbox"]; unset( $_SESSION[$precookie."x_eventbox"] ); }
	function x_eventBoxSet($precookie = "") { if(isset($_SESSION[$precookie."x_eventbox"])) { return true; } else { return false; } }
?>