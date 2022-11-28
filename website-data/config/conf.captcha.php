<?php
	/* 	__________ ____ ___  ___________________.___  _________ ___ ___  
		\______   \    |   \/  _____/\_   _____/|   |/   _____//   |   \ 
		 |    |  _/    |   /   \  ___ |    __)  |   |\_____  \/    ~    \
		 |    |   \    |  /\    \_\  \|     \   |   |/        \    Y    /
		 |______  /______/  \______  /\___  /   |___/_______  /\___|_  / 
				\/                 \/     \/                \/       \/  Captcha Configuration */
		define('_CAPTCHA_FONT_',   	 _MAIN_FOLDER_."/font/font_captcha.ttf");
		define('_CAPTCHA_WIDTH_',    "200"); 
		define('_CAPTCHA_HEIGHT_',   "70");	
		define('_CAPTCHA_SQUARES_',   mt_rand(4, 15));	
		define('_CAPTCHA_ELIPSE_',    mt_rand(4, 15));	
		define('_CAPTCHA_RANDOM_',    mt_rand(1000, 9999));
?>