<?php
if(isset($_POST["exec_edit"]) AND trim(@$_POST["domain"]) != "") {
	if(mrod_domain_id_exists($mysql, @$_POST["exec_ref"])) {
			if(is_string($_POST["ovr_host"])) { $mysql->query("UPDATE "._TABLE_DOMAIN_." SET ovr_servername = '".htmlspecialchars($_POST["ovr_host"])."' WHERE id = \"".$_POST["exec_ref"]."\";"); }
			else { $mysql->query("UPDATE "._TABLE_DOMAIN_." SET ovr_servername = NULL WHERE id = \"".$_POST["exec_ref"]."\";"); }
			
			if(is_numeric($_POST["ovr_port"])) { $mysql->query("UPDATE "._TABLE_DOMAIN_." SET ovr_serverport = '".htmlspecialchars($_POST["ovr_port"])."' WHERE id = \"".$_POST["exec_ref"]."\";"); }
			else { $mysql->query("UPDATE "._TABLE_DOMAIN_." SET ovr_serverport = NULL WHERE id = \"".$_POST["exec_ref"]."\";"); }
		if(@$_POST["ovr_smtps"]) { $ovr_smtps = 1; } else { $ovr_smtps = "NULL"; }
		if(@$_POST["ovr_smtp"]) { $ovr_smtps = 0; }			
			$mysql->query("UPDATE "._TABLE_DOMAIN_." SET ovr_smtps = $ovr_smtps WHERE id = \"".$_POST["exec_ref"]."\";");
			$mysql->query("UPDATE "._TABLE_DOMAIN_." SET fk_relay = \"".$_POST["fk_relay"]."\" WHERE id = \"".$_POST["exec_ref"]."\";");
			
			x_eventBoxPrep("Domain has been updated!", "ok", _COOKIES_);
	} else {
		if(mrod_relay_id_exists($mysql, @$_POST["fk_relay"])) { $fk_relay = $_POST["fk_relay"]; } else { $fk_relay = "NULL"; } 
		if(trim(@$_POST["ovr_host"]) != "")  { $ovr_host = $_POST["ovr_host"]; } else { $ovr_host = "NULL"; } 
		if(trim(@$_POST["ovr_port"]) != "")  { $ovr_port = $_POST["ovr_port"]; } else { $ovr_port = "NULL"; }
		if(@$_POST["ovr_smtps"]) { $ovr_smtps = 1; } else { $ovr_smtps = "NULL"; }
		if(@$_POST["ovr_smtp"]) { $ovr_smtps = 0; }
													
		$mysql->query("INSERT INTO "._TABLE_DOMAIN_." (domain, fk_relay, fk_user, type, ovr_servername, ovr_serverport, ovr_smtps) 
													VALUES (\"".$mysql->escape(trim($_POST["domain"]))."\"
													, ".$fk_relay."
													, ".$user->user_id."
													, \"usr\"
													, \"".$mysql->escape($ovr_host)."\"
													, ".$mysql->escape($ovr_port)."
													, ".$mysql->escape($ovr_smtps).");");
		x_eventBoxPrep("Domain has been added!", "ok", _COOKIES_);
	}
}

if(isset($_POST["exec_del"])) {
	if(is_numeric($_POST["exec_ref"])) {
		$mysql->query("DELETE FROM "._TABLE_DOMAIN_." WHERE id = \"".htmlspecialchars($_POST["exec_ref"])."\";");
		x_eventBoxPrep("Domain has been deleted!", "ok", _COOKIES_);
	} 
}
	
	require_once("./templates/template_header.php");
	
	echo '<div class="content_box"><a href="./?site=domains&edit=add">Add new Domain</a>';

		
		if($user->user_rank != 1) { $curissue	=	mysqli_query($mysql->mysqlcon, "SELECT *	FROM "._TABLE_DOMAIN_." WHERE type NOT LIKE  '%dns%' ORDER BY id DESC"); }
		else { $curissue	=	mysqli_query($mysql->mysqlcon, "SELECT *	FROM "._TABLE_DOMAIN_." WHERE fk_user = '".$user->user_id."' AND type NOT LIKE  '%dns%' ORDER BY id DESC");  }

		while ($curissuer	=	mysqli_fetch_array($curissue) ) { 
			if($getrelay = mrod_relay_get($mysql, @$curissuer["fk_relay"])) {  
				$finalhost = $getrelay["servername"]; 
				$finalport = $getrelay["port"]; 	
				if($getrelay["smtps"] != 1) { $finalcon =  "smtp:"; } else { $finalcon =  "smtps:"; }
			}
		
			if(trim(@$curissuer["ovr_servername"]) != "") { $finalhost = $curissuer["ovr_servername"]; }
			if(is_numeric($curissuer["ovr_serverport"])) { $finalport = $curissuer["ovr_serverport"]; }
			

			
			if(@mrod_relay_get($mysql, $curissuer["fk_relay"])["smtps"] == 1) { $finalcon1 =  "smtps:"; }
			elseif(@mrod_relay_get($mysql, $curissuer["fk_relay"])["smtps"] == 0) { $finalcon1 =  "smtp:"; }
			
			if(@$curissuer["ovr_smtps"] == 1) { $finalcon =  "smtps:"; }
			elseif(@$curissuer["ovr_smtps"] == 0 AND is_numeric(@$curissuer["ovr_smtps"])) { $finalcon =  "smtp:"; }
			else { $finalconx =  $finalcon1; $finalcon =  ""; }			
			
			echo '<fieldset><legend>'.@$curissuer["domain"].'</legend>';
				echo "<div style='width: 50%;float:left;'>";
					echo "<b>Actual Relay: ".$finalconx.$finalhost.":".@$finalport."</b><br />";
					echo "<b style='color: #555555'>Override Relay: ".$finalcon.@mrod_domain_get($mysql, $curissuer["id"])["ovr_servername"].":".@mrod_domain_get($mysql, $curissuer["id"])["ovr_serverport"]."</b><br />";
					echo "<b style='color: #555555'>Related Relay: ".$finalcon1.@mrod_relay_get($mysql, $curissuer["fk_relay"])["servername"].":".@mrod_relay_get($mysql, $curissuer["fk_relay"])["port"]."</b><br />";
					echo "Owner: ".mrod_user_get_name_from_id($mysql, $curissuer["fk_user"])."<br />";
				
				echo "</div>";
				echo "<div style='width: 15%;float:left;'>".$curissuer["type"]."</div>";	
				
				echo "<div style='width: 20%;float:left;'>";	
					echo "<a href='./?site=domains&edit=".$curissuer["id"]."'>Edit</a>";
					echo "<a href='./?site=domains&delete=".$curissuer["id"]."'>Delete</a>";
				echo "</div>";	
			echo '</fieldset>';	
		}
	
	echo '</div>';
?>	
<?php if(mrod_domain_id_exists($mysql, @$_GET["edit"]) OR @$_GET["edit"] == "add") { 
		if(@$_GET["edit"] == "add") { $title = "Add new Domain"; } else { $title = "Edit Domain: ".mrod_domain_get($mysql, $_GET["edit"])["domain"]; } ?>
	
	<div class="internal_popup">
		<div class="internal_popup_inner">
			<div class="internal_popup_title"><?php echo $title; ?></div>		
			<form method="post" action="./?site=domains"><div class="internal_popup_content">

				<input type="text" placeholder="Domain-Name" name="domain" value="<?php echo mrod_domain_get($mysql, $_GET["edit"])["domain"]; ?>">
			<?php
						echo "<select name='fk_relay'>";
						$curissue343423	=	mysqli_query($mysql->mysqlcon, "SELECT *FROM "._TABLE_RELAY_." ORDER BY id DESC");	
					echo "<option value='".mrod_domain_get($mysql, $_GET["edit"])["fk_relay"]."'>No Change</option>";	
					echo "<option value='0'>No Relay</option>";												
						while ($curissuer11	=	mysqli_fetch_array($curissue343423) ) { 
							echo "<option value='".$curissuer11["id"]."'>".$curissuer11["servername"]." ".@$curissuer11["port"]."</option>";		
						} echo "</select>";			
			
			?>
				
				<input type="text" placeholder="Relay-Host" name="ovr_host" value="<?php echo mrod_domain_get($mysql, $_GET["edit"])["ovr_servername"]; ?>">
				<input type="text" placeholder="Relay-Port" name="ovr_port" value="<?php echo mrod_domain_get($mysql, $_GET["edit"])["ovr_serverport"]; ?>">
				<input type="checkbox" name="ovr_smtps" <?php if(mrod_domain_get($mysql, $_GET["edit"])["ovr_smtps"] == 1) { echo "checked"; } ?>> Use SMTPS Override
				<input type="checkbox" name="ovr_smtp" <?php if(mrod_domain_get($mysql, $_GET["edit"])["ovr_smtps"] == 0 AND is_numeric(mrod_domain_get($mysql, $_GET["edit"])["ovr_smtps"])) { echo "checked"; } ?>> Use SMTP Override
				<?php if(is_numeric(@$_GET["edit"])) { ?><input type="hidden" value="<?php echo @$_GET["edit"]; ?>" name="exec_ref"><?php } ?>
			</div>		
			<div class="internal_popup_submit"><input type="submit" value="Execute" name="exec_edit"><a href="./?site=domains">Cancel</a></div></form>
		</div>
	</div>
<?php } ?>
<?php if(mrod_domain_id_exists($mysql, @$_GET["delete"])) { ?>	
	<div class="internal_popup">
		<form method="post" action="./?site=domains"><div class="internal_popup_inner">
		<?php if(is_numeric(@$_GET["delete"])) { ?><input type="hidden" value="<?php echo @$_GET["delete"]; ?>" name="exec_ref"><?php } ?>
			<div class="internal_popup_title">Delete: <?php echo mrod_domain_get($mysql, $_GET["delete"])["domain"]; ?></div>
			<div class="internal_popup_submit"><input type="submit" value="Execute" name="exec_del"><?php if(is_numeric(@$_GET["delete"])) { ?><input type="hidden" value="<?php echo @$_GET["delete"]; ?>" name="exec_ref"><?php } ?><a href="./?site=domains">Cancel</a></div>		
		</div></form>
	</div>
<?php } ?>