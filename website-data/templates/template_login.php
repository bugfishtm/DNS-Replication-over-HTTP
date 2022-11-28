<?php		
				
	if(isset($_POST["auth"])) {
		if(@$_SESSION[_COOKIES_."captcha"] == @$_POST["captcha"] AND isset($_SESSION[_COOKIES_."captcha"])) {
			if($csrf->check(@$_POST["csrf"])) {
				if(!$ipbl->isblocked()) {
					if(isset($_POST["username"]) AND isset($_POST["password"])) {
						$user->login_request(@$_POST["username"], @$_POST["password"]);
							if ($user->login_request_code == 1) {
								x_eventBoxPrep("Login successfull!", "ok", _COOKIES_);
								Header("Location: ".htmlspecialchars(@$_SERVER['REQUEST_URI']));
								exit();
							}
							if ($user->login_request_code == 2) {
								x_eventBoxPrep("Wrong Username/Password!", "error", _COOKIES_); $ipbl->raise();
							}
							elseif ($user->login_request_code == 3) {
								x_eventBoxPrep("Wrong Username/Password!", "error", _COOKIES_); $ipbl->raise();
							}
							elseif ($user->login_request_code == 4) {
								x_eventBoxPrep("This user is blocked!", "error", _COOKIES_);
							} else { $ipbl->raise(); }			
					} 
				} else { x_eventBoxPrep("IP is currently blocked!", "error", _COOKIES_); } 
			} else { x_eventBoxPrep("CSRF error, please retry!", "error", _COOKIES_); } 
		} else { x_eventBoxPrep("Captcha is wrong!", "error", _COOKIES_); } 
	} 
					
	require_once("./templates/template_header.php"); ?>
	<div class="content_box">
		<form method="post">
			<input type="hidden"	name="csrf"			value="<?php echo $csrf->get(); ?>">
			<input type="text" 		name="username" 	placeholder="us3rn4me" >
			<input type="password"  name="password" 	placeholder="p3ssw0rd">
			<img src="./captcha/captcha_login.php"><input type="text"  name="captcha" 	placeholder="c4ptcha">
			<input type="submit" 	value="Authenticate" name="auth" class="primary_button">
		</form>
	</div>

