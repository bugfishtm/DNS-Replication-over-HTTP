<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if ($csrf->check($_POST['csrf'])) {
		if (@$_POST["password1"] == @$_POST["password2"]) {
			if (trim(@$_POST["password1"]) != "") {
				$user->changeUserPass($user->user_id, $_POST["password1"]) ;
				x_eventBoxPrep("Password has been changed!", "ok", _COOKIES_);
			} else  { x_eventBoxPrep("Passwords can not be empty!", "error", _COOKIES_); }
		} else  { x_eventBoxPrep("Passwords are not identical!", "error", _COOKIES_); }
	} else  { x_eventBoxPrep("CSRF Error - Retry!", "error", _COOKIES_); }
}  $csrftoken =	mt_rand(100000,9999999); $_SESSION[_COOKIES_.'csrf'] = $csrftoken; 
   $query = "SELECT * FROM `"._TABLE_USER_."` WHERE id = ".$_SESSION['mrdns_userid']." ORDER BY id DESC LIMIT 15 ";	
require_once("./templates/template_header.php");


	echo '<div class="content_box">';
		echo '<form method="post">';
			echo "<input name='password1' type='password' placeholder='Password'><br clear='both'/>";
			echo "<input name='password2' type='password' placeholder='Confirm Password'><br clear='both'/>";
			echo "<input name='updatepass' type='submit' value='Change Password'><br clear='both'/>";
			echo "<input name='csrf' type='hidden' value='".$csrf->get()."'>";
		echo '</form>';
	echo '</div>';
?>