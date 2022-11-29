<?php

if(!$permsobj->hasPerm($user->user_id, "perm_admin") AND $user->user_rank != 0) { require_once("./templates/template_header.php"); echo "<div class='content_box'>No Permission!</div>"; } else {

if(isset($_POST["exec_edit"]) AND trim(@$_POST["username"]) != "") {
	if($user->exists(@$_POST["exec_ref"])) {	
		$userid = @$_POST["exec_ref"]; 
		$user->changeUserName($userid, trim(@$_POST["username"]));
		if(isset($_POST["perm_admin"])) { $permsobj->addPerm($userid, "perm_admin"); } else { $permsobj->removePerm($userid, "perm_admin"); }
		x_eventBoxPrep("User has been updated!", "ok", _COOKIES_);	
	} else {											
		$user->addUser(@$_POST["username"], "undefined", @$_POST["password"], 1, 1);
		x_eventBoxPrep("User has been added!", "ok", _COOKIES_);
		$userid = $mysql->insert_id;
		if(isset($_POST["perm_admin"])) { $permsobj->addPerm($userid, "perm_admin"); } else { $permsobj->removePerm($userid, "perm_admin"); }
	}
}

if(isset($_POST["exec_del"]) AND $user->exists(@$_POST["exec_ref"])) {
	if(is_numeric($_POST["exec_ref"])) {
		$user->deleteUser($_POST["exec_ref"]);
		$permsobj->flush($_POST["exec_ref"]);
		x_eventBoxPrep("User has been deleted!", "ok", _COOKIES_);
	} 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' AND isset($_POST["exec_pw"])) {
	if ($csrf->check($_POST['csrf'])) {
		if (@$_POST["password1"] == @$_POST["password2"]) {
			if (trim(@$_POST["password1"]) != "") {
				$user->changeUserPass($_POST["exec_ref"], $_POST["password1"]) ;
				x_eventBoxPrep("Password has been changed!", "ok", _COOKIES_);
			} else  { x_eventBoxPrep("Passwords can not be empty!", "error", _COOKIES_); }
		} else  { x_eventBoxPrep("Passwords are not identical!", "error", _COOKIES_); }
	} else  { x_eventBoxPrep("CSRF Error - Retry!", "error", _COOKIES_); }
} 
	
	require_once("./templates/template_header.php");
	
	echo '<div class="content_box"><a href="./?site=users&edit=add">Add new User</a>';
		
		$curissue	=	mysqli_query($mysql->mysqlcon, "SELECT *	FROM "._TABLE_USER_."  ORDER BY id DESC"); 

		while ($curissuer	=	mysqli_fetch_array($curissue) ) { 
			echo '<fieldset><legend>'.@$curissuer["user_name"].'</legend>';
				echo "<div style='width: 60%;float:left;'>";
					echo "Created: ".@$curissuer["created_date"]."<br />";
					echo "Perms:";
					$ar = $permsobj->getPerm($curissuer["id"]);
					if(is_array($ar)) {
						foreach($ar AS $key => $value) {
							echo $value.",";
						}
					} if($curissuer["user_rank"] == 0) { echo " Administrator - All Permissions"; }
					echo "<br />";
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
		<?php if(@$_GET["edit"] == "add") { ?>		<input type="text" placeholder="Hostname" name="username" > <?php  ?>
		<?php } else { ?>		<input type="text" placeholder="Hostname" name="username" value="<?php echo $user->getInfo($_GET["edit"])["user_name"]; ?>"> <?php } ?>
		<?php if(@$_GET["edit"] == "add") { ?> <input type="password" placeholder="Password" name="password"> <?php } ?>
				<div style="float:left">
				<?php if($user->getInfo($_GET["edit"])["user_rank"] != 0 OR @$_GET["edit"] == "add") { $curid = $user->getInfo($_GET["edit"])["id"]; ?>		
					<div style="float:left"><input type="checkbox" name="perm_admin" <?php if($permsobj->hasPerm($curid, "perm_admin")) { echo "checked"; } ?>> Administrator Area	</div>
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
			<div class="internal_popup_title">Delete: <?php echo $user->getInfo($_GET["delete"])["user_name"]; ?></div>
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
<?php if($user->exists(@$_GET["pass"])) { 	
   $query = "SELECT * FROM `"._TABLE_USER_."` WHERE id = ".@$_GET["pass"]."";	 ?>
	<div class="internal_popup">
		<form method="post" action="./?site=users"><div class="internal_popup_inner">
			<div class="internal_popup_title">Change Pass: <?php echo $user->getInfo($_GET["pass"])["user_name"]; ?></div>
			<div class="internal_popup_content">	
				<input type="password" placeholder="Password" name="password1">
				<input type="password" placeholder="Password" name="password2">
				<input type="hidden" name="csrf" value="<?php echo $csrf->get(); ?>">
			</div>
			<div class="internal_popup_submit"><input type="hidden" value="<?php echo @$_GET["pass"]; ?>" name="exec_ref"><input type="submit" value="Execute" name="exec_pw"><a href="./?site=users">Cancel</a></div>	</form>	
		</div>
	</div>
<?php } 
}?>