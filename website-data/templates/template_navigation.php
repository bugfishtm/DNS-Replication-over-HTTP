<?php
	/*
		__________              _____.__       .__     
		\______   \__ __  _____/ ____\__| _____|  |__  
		 |    |  _/  |  \/ ___\   __\|  |/  ___/  |  \ 
		 |    |   \  |  / /_/  >  |  |  |\___ \|   Y  \
		 |______  /____/\___  /|__|  |__/____  >___|  /
				\/     /_____/               \/     \/  Navigation File */
				if($user->user_loggedIn) { ?>
<div id="nav">
	<?php if($user->user_rank == 0) {  echo "Admin: "; ?>
		<?php if($permsobj->hasPerm($user->user_id, "perm_admin") OR $user->user_rank == 0) { ?> <a href="./?site=users" <?php if(@$_GET["site"] == "users") { echo 'id="nav_active"'; } ?>>Users</a> <?php } ?> 
		<?php if($permsobj->hasPerm($user->user_id, "perm_admin")  OR $user->user_rank == 0) { ?> <a href="./?site=status" <?php if(@$_GET["site"] == "status") { echo 'id="nav_active"'; } ?>>Status</a> <?php } ?> 
		<?php if($permsobj->hasPerm($user->user_id, "perm_admin")  OR $user->user_rank == 0) { ?> <a href="./?site=blocks" <?php if(@$_GET["site"] == "blocks") { echo 'id="nav_active"'; } ?>>Blacklist(Login)</a><?php } ?> 
	<?php } else { echo "Welcome ".$user->user_name."!"; } ?>	
	
	<br clear="left">
	<a href="./?site=binddomains" <?php if(@$_GET["site"] == "binddomains") { echo 'id="nav_active"'; } ?>>Bind-Domains</a> 
	<a href="./?site=apidomains" <?php if(@$_GET["site"] == "apidomains") { echo 'id="nav_active"'; } ?>>API-Domains</a> 
	<a href="./?site=server" <?php if(@$_GET["site"] == "server") { echo 'id="nav_active"'; } ?>>Server</a>
	<a href="./?site=profile" <?php if(@$_GET["site"] == "profile") { echo 'id="nav_active"'; } ?>>Profile</a>  
	<a href="./?site=logout">Logout</a>		
</div>	

<?php } ?>
<div id="contentwrapper"></div>
<div id="content">