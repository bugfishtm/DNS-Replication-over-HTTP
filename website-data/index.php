<?php
	/*
		__________              _____.__       .__     
		\______   \__ __  _____/ ____\__| _____|  |__  
		 |    |  _/  |  \/ ___\   __\|  |/  ___/  |  \ 
		 |    |   \  |  / /_/  >  |  |  |\___ \|   Y  \
		 |______  /____/\___  /|__|  |__/____  >___|  /
				\/     /_____/               \/     \/  Index File */
	require_once("./settings.php");
	
	if(is_numeric(_IP_BLACKLIST_DAILY_OP_LIMIT_)) { $ipbl = new x_class_ipblacklist($mysql->mysqlcon, _TABLE_IPBL_, _IP_BLACKLIST_DAILY_OP_LIMIT_); } 
		else { $ipbl = new x_class_ipblacklist($mysql->mysqlcon, _TABLE_IPBL_, 1000); }
	if(is_numeric(_CSRF_VALID_LIMIT_TIME_)) { $csrf = new x_class_csrf(_COOKIES_, _CSRF_VALID_LIMIT_TIME_); }
		else { $csrf = new x_class_csrf(_COOKIES_, 300); }
	
	if($user->loggedIn) {			
		$permsobj = new x_class_simplePerms($mysql->mysqlcon, _TABLE_PERM_);
		switch($_GET["site"]) {
			case "logout": define("_INIT_TITLE_", "Logout | DNSHTTP"); $user->logout(); Header("Location: ./"); exit(); break;
			case "apidomains": define("_INIT_TITLE_", "API-Domains | DNSHTTP"); require_once("./sections/apidomains.php"); break;
			case "binddomains": define("_INIT_TITLE_", "Bind-Domains | DNSHTTP"); require_once("./sections/binddomains.php"); break;
			case "blocks": define("_INIT_TITLE_", "IP-Blacklist | DNSHTTP"); require_once("./sections/blocks.php"); break;
			case "server": define("_INIT_TITLE_", "Server | DNSHTTP"); require_once("./sections/server.php"); break;
			case "users": define("_INIT_TITLE_", "Users | DNSHTTP"); require_once("./sections/users.php"); break;
			case "status": define("_INIT_TITLE_", "Status | DNSHTTP"); require_once("./sections/status.php"); break;
			case "profile": define("_INIT_TITLE_", "Profile | DNSHTTP"); require_once("./sections/profile.php"); break;
			default: Header("Location: ./?site=binddomains"); exit();				
		};
		
		x_eventBoxShow(_COOKIES_);
		require_once("./templates/template_footer.php");		
	} else {
		define("_INIT_TITLE_", "Login | DNSHTTP");
		require_once("./templates/template_login.php");
		x_eventBoxShow(_COOKIES_);
		require_once("./templates/template_footer.php");
	}
 ?>