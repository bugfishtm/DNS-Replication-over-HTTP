<?php
	/*
		__________              _____.__       .__     
		\______   \__ __  _____/ ____\__| _____|  |__  
		 |    |  _/  |  \/ ___\   __\|  |/  ___/  |  \ 
		 |    |   \  |  / /_/  >  |  |  |\___ \|   Y  \
		 |______  /____/\___  /|__|  |__/____  >___|  /
				\/     /_____/               \/     \/  DNSHTTP Configuration File */
		#####################################################################
		##### Site Setup       ##############################################				
		define("_TITLE_", 				""); # A Imaginary Server Name to show at Title	 		
		define("_COOKIES_",     		"dnshttp_"); # Cookie Prefix			
		define("_MAIN_PATH_",			"/var/www/html/dnshttp/"); # MAIN PATH

		#####################################################################
		##### Security Setup   ##############################################		
		define("_LOGIN_SESSION_BLOCK_LIMIT_", 			false);
		define("_IP_BLACKLIST_DAILY_OP_LIMIT_", 		false);
		define("_CSRF_VALID_LIMIT_TIME_", 				false);
		
		#####################################################################
		##### MySQL Setup      ##############################################				
		define("_SQL_HOST_", 			"");
		define("_SQL_USER_", 			"");
		define("_SQL_PASS_", 			"");
		define("_SQL_DB_", 				"");
		
		#####################################################################
		##### POSTFIX FILES    ##############################################	
		define("_CRON_BIND_LIB_", 				"/var/lib/bind/");
		define("_CRON_BIND_CACHE_", 			"/var/cache/bind/");
		define("_CRON_BIND_FILE_DNSHTTP_",  	"/etc/bind/dnshttp.conf.local");
		define("_CRON_BIND_FILE_",  			"/etc/bind/named.conf.local");
		
		#####################################################################
		##### TABLES           ##############################################						
		define('_TABLE_PREFIX_',  				"dnshttp_");	
		define('_TABLE_USER_',   				_TABLE_PREFIX_."user");  
		define('_TABLE_USER_SESSION_',			_TABLE_PREFIX_."user_session");
		define('_TABLE_DOMAIN_BIND_',			_TABLE_PREFIX_."bind_domain");
		define('_TABLE_DOMAIN_BINDED_',			_TABLE_PREFIX_."binded_domain");
		define('_TABLE_DOMAIN_API_',			_TABLE_PREFIX_."api_domain");
		define('_TABLE_SERVER_',				_TABLE_PREFIX_."server");
		define('_TABLE_CONFLICT_',				_TABLE_PREFIX_."conflict");
		define('_TABLE_IPBL_',					_TABLE_PREFIX_."ipblacklist");
		define('_TABLE_PERM_',					_TABLE_PREFIX_."perms");
		
		#####################################################################
		##### INCLUDES           ##############################################	
		define("_MAIN_FOLDER_", _MAIN_PATH_);
		foreach (glob(_MAIN_PATH_."library/dnshttp_*.php") as $filename){require_once $filename;}
		foreach (glob(_MAIN_PATH_."vendor/functions/x_*.php") as $filename){require_once $filename;}
		foreach (glob(_MAIN_PATH_."vendor/classes/x_*.php") as $filename){require_once $filename;}
		foreach (glob(_MAIN_PATH_."config/conf.*.php") as $filename){require_once $filename;}
?>