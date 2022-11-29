<?php 
if(!$permsobj->hasPerm($user->user_id, "perm_admin") AND $user->user_rank != 0) { echo "You do not have Permission!"; } else {
require_once("./templates/template_header.php"); ?>

<div class="content_box">

	<fieldset>
		<legend>Default Settings</legend>
		<?php
			echo 'Servername: <b>'._TITLE_.'</b><br />';
			echo 'Cookiename: <b>'._COOKIES_.'</b><br />';
			echo 'Document-Root: <b>'._MAIN_PATH_.'</b><br />';
		?>
	</fieldset>

</div><div class="content_box">

	<fieldset>
		<legend>Security Settings</legend>
		<?php
			if(_LOGIN_SESSION_BLOCK_LIMIT_ == false) { $tmp = "Deactivated"; } else { $tmp = _LOGIN_SESSION_BLOCK_LIMIT_; }
			echo 'Login Session Security Limit: <b>'.$tmp.'</b><br />';
			if(_IP_BLACKLIST_DAILY_OP_LIMIT_  == false) { $tmp = "1000"; } else { $tmp = _LOGIN_SESSION_BLOCK_LIMIT_; }
			echo 'IP Blacklist Daily Limit: <b>'.$tmp.'</b><br />';
			if(_CSRF_VALID_LIMIT_TIME_  == false) { $tmp = "120"; } else { $tmp = _LOGIN_SESSION_BLOCK_LIMIT_; }
			echo 'CSRF Validation Time: <b>'.$tmp.'</b><br />';
		?>
	</fieldset>

</div><div class="content_box">

	<?php 
		switch($user->user_rank) {
			case 0: $rank = "Administrator"; break;
			default: $rank = "User"; break;
		}
	?>
	
	<fieldset>
		<legend>Login Informations</legend>
		<?php
			echo 'Username: <b>'.$user->user_name.'</b><br />';
			echo 'Rank: <b>'.$rank.'</b><br />';
			echo 'IP: <b>'.$_SERVER["REMOTE_ADDR"].'</b><br />';
		?>
	</fieldset>

</div><div class="content_box">

	<fieldset>
		<legend>Cronjob Settings</legend>
		<?php
			
			echo 'Bind Lib Folder: <b>'._CRON_BIND_LIB_.'</b><br />';
			echo 'Bind Cache Folder: <b>'._CRON_BIND_CACHE_.'</b><br />';
			echo 'Named File for Domains: <b>'._CRON_BIND_FILE_.'</b><br />';
		?>
	</fieldset>

</div>


<?php } ?>