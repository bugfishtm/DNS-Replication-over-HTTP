<?php
if($permsobj->hasPerm($user->user_id, "perm_relay")  OR $user->user_rank == 0) {
if(isset($_POST["exec_edit"]) AND trim(@$_POST["hostname"]) != "" AND is_numeric(@$_POST["port"])) {
	if(is_numeric(@$_POST["exec_ref"])) {
		$mysql->query("UPDATE "._TABLE_RELAY_." SET servername = '".$mysql->escape(trim($_POST["hostname"]))."' WHERE id = \"".$_POST["exec_ref"]."\";");
		$mysql->query("UPDATE "._TABLE_RELAY_." SET port = '".$mysql->escape(trim($_POST["port"]))."' WHERE id = \"".$_POST["exec_ref"]."\";");
		if($_POST["smtps"]) { $smtps = 1; } else  { $smtps = 0 ;}
		$mysql->query("UPDATE "._TABLE_RELAY_." SET smtps = '".$smtps."' WHERE id = \"".$_POST["exec_ref"]."\";");
		x_eventBoxPrep("Relay has been updated!", "ok", _COOKIES_);	
	} else {											
		if($_POST["smtps"]) { $smtps = 1; } else  { $smtps = 0 ;}
		$mysql->query("INSERT INTO "._TABLE_RELAY_." (servername, port, smtps, fk_user) 
													VALUES (\"".$mysql->escape(trim($_POST["hostname"]))."\"
													, '".$mysql->escape(trim($_POST["port"]))."'
													, '".$smtps."'
													, '".$user->user_id."');");
		x_eventBoxPrep("Relay has been added!", "ok", _COOKIES_);
	}
}

if(isset($_POST["exec_del"]) AND mrod_relay_id_exists($mysql, @$_POST["exec_ref"])) {
	if(is_numeric($_POST["exec_ref"])) {
		$res = $mysql->select("SELECT * FROM "._TABLE_DOMAIN_." WHERE fk_relay = '".$_POST["exec_ref"]."'", false);
		if(!is_array($res)) {
			$mysql->query("DELETE FROM `"._TABLE_RELAY_."` WHERE id = \"".$_POST["exec_ref"]."\";");
			x_eventBoxPrep("Relay has been deleted!", "ok", _COOKIES_);
		} else { x_eventBoxPrep("Relay is in use for different Domains!", "error", _COOKIES_); }
	} 
}
	
	require_once("./templates/template_header.php");
	
	echo '<div class="content_box"><a href="./?site=relay&edit=add">Add new Relay</a>';
		
		$curissue	=	mysqli_query($mysql->mysqlcon, "SELECT *	FROM "._TABLE_RELAY_."  ORDER BY id DESC");

		while ($curissuer	=	mysqli_fetch_array($curissue) ) { 
			if($curissuer["smtps"] != 1) { $finalcon =  "smtp"; } else { $finalcon =  "smtps"; }
			echo '<fieldset><legend>Relay-ID: '.@$curissuer["id"].'</legend>';
				echo "<div style='width: 60%;float:left;'>";
					echo "<b>".$finalcon.":".@$curissuer["servername"].":".@$curissuer["port"]."</b><br />";
					echo "Owner: ".mrod_user_get_name_from_id($mysql, $curissuer["fk_user"])."<br />";
					echo "DNS: "._CRON_TXT_TO_RELAY_STRING_.@$curissuer["id"]."<br />";
				echo "</div>";	
				
				echo "<div style='width: 20%;float:left;'>";	
					echo "<a href='./?site=relay&test=".$curissuer["id"]."'>Test</a>";
					echo "<a href='./?site=relay&edit=".$curissuer["id"]."'>Edit</a>";
					echo "<a href='./?site=relay&delete=".$curissuer["id"]."'>Delete</a>";
				echo "</div>";	
			echo '</fieldset>';	
		}
	
	echo '</div>';
?>	
<?php if(mrod_relay_id_exists($mysql, @$_GET["edit"]) OR @$_GET["edit"] == "add") { 
		if(@$_GET["edit"] == "add") { $title = "Add new Relay"; } else { $title = "Edit Relay: ".mrod_relay_get($mysql, $_GET["edit"])["servername"]; } ?>
	
	<div class="internal_popup">
		<div class="internal_popup_inner">
			<div class="internal_popup_title"><?php echo $title; ?></div>		
			<form method="post" action="./?site=relay"><div class="internal_popup_content">			
				<input type="text" placeholder="Hostname" name="hostname" value="<?php echo mrod_relay_get($mysql, $_GET["edit"])["servername"]; ?>">
				<input type="number" placeholder="Port" name="port" maxlength="5"  value="<?php echo mrod_relay_get($mysql, $_GET["edit"])["port"]; ?>">
				<input type="checkbox" name="smtps" <?php if(mrod_relay_get($mysql, $_GET["edit"])["smtps"] == 1) { echo "checked"; } ?>> Use SMTPS for Relay Settings
				<?php if(is_numeric(@$_GET["edit"])) { ?><input type="hidden" value="<?php echo @$_GET["edit"]; ?>" name="exec_ref"><?php } ?>
			</div>		
			<div class="internal_popup_submit"><input type="submit" value="Execute" name="exec_edit"><a href="./?site=relay">Cancel</a></div></form>
		</div>
	</div>
<?php } ?>
<?php if(mrod_relay_id_exists($mysql, @$_GET["delete"])) { ?>	
	<div class="internal_popup">
		<form method="post" action="./?site=relay"><div class="internal_popup_inner">
			<div class="internal_popup_title">Delete: <?php echo mrod_relay_get($mysql, $_GET["delete"])["servername"]; ?></div>
			<div class="internal_popup_submit"><input type="hidden" value="<?php echo @$_GET["delete"]; ?>" name="exec_ref"><input type="submit" value="Execute" name="exec_del"><a href="./?site=relay">Cancel</a></div>		
		</div></form>
	</div>
<?php } ?>
<?php if(mrod_relay_id_exists($mysql, @$_GET["test"])) { ?>	
	<div class="internal_popup">
		<div class="internal_popup_inner">
			<div class="internal_popup_title">Test: <?php echo mrod_relay_get($mysql, $_GET["test"])["servername"]; ?></div>
			<div class="internal_popup_content"><?php if(mrod_smtp_check(mrod_relay_get($mysql, $_GET["test"])["servername"], mrod_relay_get($mysql, $_GET["test"])["port"])) { echo "SMTP Server is <b>FAILING</b>!"; } else { echo "SMTP Server is <b>OK</b>!"; } ?> </div>
			<div class="internal_popup_submit"><a href="./?site=relay">Cancel</a></div>		
		</div>
	</div>
<?php }
} ?>