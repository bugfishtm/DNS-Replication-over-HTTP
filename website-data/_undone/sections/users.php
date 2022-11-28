<?php

if(!$permsobj->hasPerm($user->user_id, "perm_admin") AND $user->user_rank != 0) { echo "You do not have Permission!"; } else {

if(isset($_POST["exec_edit"]) AND trim(@$_POST["username"]) != "") {
	if($user->exists(@$_POST["exec_ref"])) {	
		$userid = @$_POST["exec_ref"]; 
		$user->changeUserName($userid, trim(@$_POST["username"]));
		if(isset($_POST["perm_admin"])) { $permsobj->addPerm($userid, "perm_admin"); } else { $permsobj->removePerm($userid, "perm_admin"); }
		if(isset($_POST["perm_od"])) { $permsobj->addPerm($userid, "perm_od"); } else { $permsobj->removePerm($userid, "perm_od"); }
		if(isset($_POST["perm_ad"])) { $permsobj->addPerm($userid, "perm_ad"); } else { $permsobj->removePerm($userid, "perm_ad"); }
		if(isset($_POST["perm_or"])) { $permsobj->addPerm($userid, "perm_or"); } else { $permsobj->removePerm($userid, "perm_or"); }
		if(isset($_POST["perm_ar"])) { $permsobj->addPerm($userid, "perm_ar"); } else { $permsobj->removePerm($userid, "perm_ar"); }
		if(isset($_POST["perm_vd"])) { $permsobj->addPerm($userid, "perm_vd"); } else { $permsobj->removePerm($userid, "perm_vd"); }
		if(isset($_POST["perm_md"])) { $permsobj->addPerm($userid, "perm_md"); } else { $permsobj->removePerm($userid, "perm_md"); }
		if(isset($_POST["perm_ab"])) { $permsobj->addPerm($userid, "perm_ab"); } else { $permsobj->removePerm($userid, "perm_ab"); }
		x_eventBoxPrep("User has been updated!", "ok", _COOKIES_);	
	} else {											
		$user->addUser(@$_POST["username"], "undefined", @$_POST["password"], 1, 1);
		x_eventBoxPrep("User has been added!", "ok", _COOKIES_);
		$userid = $mysql->insert_id;
		if(isset($_POST["perm_admin"])) { $permsobj->addPerm($userid, "perm_admin"); } else { $permsobj->removePerm($userid, "perm_admin"); }
		if(isset($_POST["perm_od"])) { $permsobj->addPerm($userid, "perm_od"); } else { $permsobj->removePerm($userid, "perm_od"); }
		if(isset($_POST["perm_ad"])) { $permsobj->addPerm($userid, "perm_ad"); } else { $permsobj->removePerm($userid, "perm_ad"); }
		if(isset($_POST["perm_or"])) { $permsobj->addPerm($userid, "perm_or"); } else { $permsobj->removePerm($userid, "perm_or"); }
		if(isset($_POST["perm_ar"])) { $permsobj->addPerm($userid, "perm_ar"); } else { $permsobj->removePerm($userid, "perm_ar"); }
		if(isset($_POST["perm_vd"])) { $permsobj->addPerm($userid, "perm_vd"); } else { $permsobj->removePerm($userid, "perm_vd"); }
		if(isset($_POST["perm_md"])) { $permsobj->addPerm($userid, "perm_md"); } else { $permsobj->removePerm($userid, "perm_md"); }
		if(isset($_POST["perm_ab"])) { $permsobj->addPerm($userid, "perm_ab"); } else { $permsobj->removePerm($userid, "perm_ab"); }
	}
}

if(isset($_POST["exec_del"]) AND $user->exists(@$_POST["exec_ref"])) {
	if(is_numeric($_POST["exec_ref"])) {
		$user->deleteUser($_POST["exec_ref"]);
		$permsobj->flush($_POST["exec_ref"]);
		x_eventBoxPrep("User has been deleted!", "ok", _COOKIES_);
	} 
}
	
	require_once("./templates/template_header.php");
	
	echo '<div class="content_box"><a href="./?site=users&edit=add">Add new User</a>';
		
		$curissue	=	mysqli_query($mysql->mysqlcon, "SELECT *	FROM "._TABLE_USER_."  ORDER BY id DESC"); 

		while ($curissuer	=	mysqli_fetch_array($curissue) ) { 
			echo '<fieldset><legend>'.@$curissuer["user_name"].'</legend>';
				echo "<div style='width: 60%;float:left;'>";
					echo "Created: ".@$curissuer["created_date"]."<br />";
					echo "Perms: ".@$curissuer["created_date"]."<br />";
				echo "</div>";	
				
				echo "<div style='width: 40%;float:left;'>";	
					echo "<a href='./?site=users&session=".$curissuer["id"]."'>Sessions</a>";
					echo "<a href='./?site=users&pass=".$curissuer["id"]."'>Password-Change</a>";
					echo "<a href='./?site=users&edit=".$curissuer["id"]."'>Edit</a>";
					if(@$curissuer["user_rank"] != 0) { echo "<a href='./?site=users&delete=".$curissuer["id"]."'>Delete</a>"; }
				echo "</div>";	
			echo '</fieldset>';
		}
	
	echo '</div>';
?>	
<?php if($user->exists(@$_GET["edit"]) OR @$_GET["edit"] == "add") { 
		if(@$_GET["edit"] == "add") { $title = "Add new User"; } else { $title = "Edit User: ".$user->getInfo($_GET["edit"])["user_name"]; } ?>
	
	<div class="internal_popup">
		<div class="internal_popup_inner">
			<div class="internal_popup_title"><?php echo $title; ?></div>		
			<form method="post" action="./?site=users"><div class="internal_popup_content">			
				<input type="text" placeholder="Hostname" name="username" value="<?php echo $user->getInfo($_GET["edit"])["user_name"]; ?>">
		<?php if(@$_GET["edit"] == "add") { ?> <input type="password" placeholder="Password" name="password" value="<?php echo $user->getInfo($_GET["edit"])["user_name"]; ?>"> <?php } ?>
				<div style="float:left">
				<?php if($user->getInfo($_GET["edit"])["user_rank"] != 0 OR @$_GET["edit"] == "add") { $curid = $user->getInfo($_GET["edit"])["id"]; ?>
					
					<div style="float:left"><input type="checkbox" name="perm_od" <?php if($permsobj->hasPerm($curid, "perm_od")) { echo "checked"; } ?>> Manage Own Domains </div>
					<div style="float:left"><input type="checkbox" name="perm_ad" <?php if($permsobj->hasPerm($curid, "perm_ad")) { echo "checked"; } ?>> Manage All Domains </div>
					
					<div style="float:left"><input type="checkbox" name="perm_or" <?php if($permsobj->hasPerm($curid, "perm_or")) { echo "checked"; } ?>> Manage Own Relays </div>
					<div style="float:left"><input type="checkbox" name="perm_ar" <?php if($permsobj->hasPerm($curid, "perm_ar")) { echo "checked"; } ?>> Manage All Domains 	</div>	
					
					<div style="float:left"><input type="checkbox" name="perm_vd" <?php if($permsobj->hasPerm($curid, "perm_vd")) { echo "checked"; } ?>> View DNS-Domains </div>
					<div style="float:left"><input type="checkbox" name="perm_md" <?php if($permsobj->hasPerm($curid, "perm_md")) { echo "checked"; } ?>> Manage DNS-Domains </div>		
					
					<div style="float:left"><input type="checkbox" name="perm_admin" <?php if($permsobj->hasPerm($curid, "perm_state")) { echo "checked"; } ?>> Administrator	</div>
				<?php } else { echo "This is the Initial User, which does have all Privileges!"; } ?>
				<?php if(is_numeric(@$_GET["edit"])) { ?><input type="hidden" value="<?php echo @$_GET["edit"]; ?>" name="exec_ref"><?php } ?>
			</div>		
			<div class="internal_popup_submit"><input type="submit" value="Execute" name="exec_edit"><a href="./?site=users">Cancel</a></div></form>
		</div>
	</div>
<?php } ?>
<?php if($user->exists(@$_GET["delete"])) { ?>	
	<div class="internal_popup">
		<div class="internal_popup_inner">
			<div class="internal_popup_title">Delete: <?php echo mrod_relay_get($mysql, $_GET["delete"])["servername"]; ?></div>
			<div class="internal_popup_submit"><form method="post" action="./?site=users"><input type="hidden" value="<?php echo @$_GET["delete"]; ?>" name="exec_ref"><input type="submit" value="Execute" name="exec_del"></form><a href="./?site=users">Cancel</a></div>		
		</div>
	</div>
<?php } ?>
<?php if($user->exists(@$_GET["session"])) { ?>	
	<div class="internal_popup">
		<div class="internal_popup_inner">
			<div class="internal_popup_title">Sessions: <?php echo $user->getInfo($_GET["session"])["user_name"]; ?></div>
			
			<?php
		
		$curissue	=	mysqli_query($mysql->mysqlcon, "SELECT * FROM "._TABLE_USER_SESSION_." WHERE user_id = '".$_GET["session"]."' ORDER BY id DESC"); 

		while ($curissuer	=	mysqli_fetch_array($curissue) ) { 
			echo '<fieldset><legend>'.@$curissuer["user_name"].'</legend>';
				echo "<div style='width: 40%;float:left;'>";
					echo "ID: ".@$curissuer["id"]."";
				echo "</div>";	
				echo "<div style='width: 60%;float:left;'>";
					echo "Last-Use: ".@$curissuer["use_date"]."";
				echo "</div>";	
			echo '</fieldset>';
		} ?>
			
			<div class="internal_popup_submit"><a href="./?site=users">Cancel</a></div>		
		</div>
	</div>
<?php } ?>
<?php if($user->exists(@$_GET["pass"])) { ?>	


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
	<div class="internal_popup">
		<form method="post" action="./?site=users"><div class="internal_popup_inner">
			<div class="internal_popup_title">Change Pass: <?php echo mrod_relay_get($mysql, $_GET["delete"])["servername"]; ?></div>
			<div class="internal_popup_content">	
				<input type="password" placeholder="Password" name="password">
				<input type="password" placeholder="Password" name="password">
			</div>
			<div class="internal_popup_submit"><input type="hidden" value="<?php echo @$_GET["delete"]; ?>" name="exec_ref"><input type="submit" value="Execute" name="exec_del"><a href="./?site=users">Cancel</a></div>	</form>	
		</div>
	</div>
<?php } 
}?>