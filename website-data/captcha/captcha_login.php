<?php  
	require_once("../settings.php");
	x_captcha(_COOKIES_."captcha", _CAPTCHA_WIDTH_, _CAPTCHA_HEIGHT_, _CAPTCHA_SQUARES_, _CAPTCHA_ELIPSE_, false, _CAPTCHA_FONT_, _CAPTCHA_RANDOM_);
?>  