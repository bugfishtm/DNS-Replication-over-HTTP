<?php
	/* 	__________ ____ ___  ___________________.___  _________ ___ ___  
		\______   \    |   \/  _____/\_   _____/|   |/   _____//   |   \ 
		 |    |  _/    |   /   \  ___ |    __)  |   |\_____  \/    ~    \
		 |    |   \    |  /\    \_\  \|     \   |   |/        \    Y    /
		 |______  /______/  \______  /\___  /   |___/_______  /\___|_  / 
				\/                 \/     \/                \/       \/  Users Configuration */
	$user = new x_class_users($mysql->mysqlcon, _TABLE_USER_, _TABLE_USER_SESSION_, _COOKIES_ );
	$user->multi_login(false);
	$user->log_sessions(true);
	$user->log_recovers(true);
	$user->log_user_mailchange(true);
	$user->dbsession_max_use_days(7);
	$user->save_ip_in_db(true);
	$user->relevant_reference_username(true);
	if(is_numeric(_LOGIN_SESSION_BLOCK_LIMIT_)) { $user->sessionban_limit(_LOGIN_SESSION_BLOCK_LIMIT_); }
	$user->init();
?>