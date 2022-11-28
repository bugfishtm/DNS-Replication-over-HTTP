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
			if(_CRON_MODE_  == 1) { $tmp = "["._CRON_MODE_."]"._CRON_MODE_1_PATH_; } else { $tmp = "Error!"; }
			if(_CRON_MODE_  == 2) { $tmp = "["._CRON_MODE_."]". _CRON_MODE_2_PATH_; } 
			echo 'Cron Order: <b>'. @_CRON_ARRAY_[0].", ".@_CRON_ARRAY_[1].", ".@_CRON_ARRAY_[2].'</b><br />';
			echo 'Cron Mode and Path: <b>'.$tmp.'</b><br />';
			if(_CRON_CLEANUP_  == true) { $tmp = "Yes"; } else { $tmp = "No"; }
			echo 'Cleanup DNS Domains on Refresh: <b>'.$tmp.'</b><br />';
			if(_CRON_DOMAIN_AS_RELAY_  == true) { $tmp = "Yes"; } else { $tmp = "No"; }
			echo '<br />Domain as Relay: <b>'.$tmp.'</b><br />';
			echo 'Port: <b>'._CRON_DOMAIN_AS_RELAY_PORT_.'</b><br />';
			echo 'Protocol: <b>'._CRON_DOMAIN_AS_RELAY_PROT_.'</b><br />';
			
			if(_CRON_SUB_AS_RELAY_  == true) { $tmp = "Yes"; } else { $tmp = "No"; }
			echo '<br />Domain Sub as Relay: <b>'.$tmp.'</b><br />';
			echo 'Subdomain Extension: <b>'._CRON_SUB_AS_RELAY_SUB_.'</b><br />';
			echo 'Port: <b>'._CRON_SUB_AS_RELAY_PORT_.'</b><br />';
			echo 'Protocol: <b>'._CRON_SUB_AS_RELAY_PROT_.'</b><br />';
			
			if(_CRON_TXT_TO_RELAY_  == true) { $tmp = "Yes"; } else { $tmp = "No"; }
			echo '<br />Domain TXT as Relay: <b>'.$tmp.'</b><br />';
			echo 'Domain TXT Identifier: <b>'._CRON_TXT_TO_RELAY_STRING_.'</b><br />';
		?>
	</fieldset>

</div>


<?php } ?>