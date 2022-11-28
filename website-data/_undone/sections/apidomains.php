<?php
	if($permsobj->hasPerm($user->user_id, "perm_vd")  OR $permsobj->hasPerm($user->user_id, "perm_md")  OR$user->user_rank == 0) {
		
if(isset($_POST["exec_edit"]) AND trim(@$_POST["domain"]) != "") {
	if(is_numeric(@$_POST["exec_ref"])) {
			$mysql->query("UPDATE "._TABLE_DOMAIN_." SET ovrserverport = '".htmlspecialchars($_POST["ovrportpost"])."' WHERE id = \"".$_POST["id"]."\";");
			x_eventBoxPrep("Domain has been updated!", "ok", _COOKIES_);
	} else {
		if(mrod_relay_id_exists($mysql, @$_POST["fk_relay"])) { $fk_relay = $_POST["fk_relay"]; } else { $fk_relay = "NULL"; } 
		if(trim(@$_POST["ovr_host"]) != "")  { $ovr_host = $_POST["ovr_host"]; } else { $ovr_host = "NULL"; } 
		if(trim(@$_POST["ovr_port"]) != "")  { $ovr_port = $_POST["ovr_port"]; } else { $ovr_port = "NULL"; }
		if(@$_POST["ovr_smtps"]) { $ovr_port = 1; } else { $ovr_smtps = 0; }

													
		$mysql->query("INSERT INTO "._TABLE_DOMAIN_." (domain, fk_relay, fk_user, type, ovr_servername, ovr_serverport, ovr_smtps) 
													VALUES (\"".$mysql->escape(trim($_POST["domain"]))."\"
													, ".$fk_relay."
													, ".$user->user_id."
													, \"".$user->user_id."\"
													, \"".$mysql->escape($ovr_host)."\"
													, ".$mysql->escape($ovr_port)."
													, ".$mysql->escape($ovr_smtps).");");
													
		x_eventBoxPrep("Domain has been added!", "ok", _COOKIES_);

	}
}

if(isset($_POST["exec_edit"]) AND trim(@$_POST["domain"]) != "") {
	if(is_numeric($_POST["exec_ref"])) {
		$mysql->query("DELETE FROM `"._TABLE_DOMAIN_."` WHERE id = \"".htmlspecialchars($_POST["id"])."\";");
		x_eventBoxPrep("Domain has been deleted!", "ok", _COOKIES_);
	} 
}
	
	require_once("./templates/template_header.php");
	
	echo '<div class="content_box">';
	
		if($user->user_rank != 1) { $curissue	=	mysqli_query($mysql->mysqlcon, "SELECT *	FROM "._TABLE_DOMAIN_." WHERE type LIKE '%dns%' ORDER BY id DESC"); }
		else { $curissue	=	mysqli_query($mysql->mysqlcon, "SELECT *	FROM "._TABLE_DOMAIN_." WHERE fk_user = '".$user->user_id."' AND type LIKE '%dns%' ORDER BY id DESC");  }

		while ($curissuer	=	mysqli_fetch_array($curissue) ) { 
			if($getrelay = mrod_relay_get($mysql, @$curissuer["fk_relay"])) {  
				$finalhost = $getrelay["servername"]; 
				$finalport = $getrelay["port"]; 	
				if($getrelay["smtps"] != 1) { $finalcon =  "smtp:"; } else { $finalcon =  "smtps:"; }
			}
		
			if(trim(@$curissuer["ovr_servername"]) != "") { $finalhost = $curissuer["ovr_servername"]; }
			if(is_numeric($curissuer["ovr_serverport"])) { $finalport = $curissuer["ovr_serverport"]; }
			
			if(@$curissuer["ovr_smtps"] === 1) { $finalcon =  "smtps:"; }
			if(@$curissuer["ovr_smtps"] === 0) { $finalcon =  "smtp:"; }
			
			echo '<fieldset><legend>'.@$curissuer["domain"].'</legend>';
				echo "<div style='width: 50%;float:left;'>";
					echo "<b>".$finalhost."</b><br />";
					echo "<b>".$finalhost.":".@$finalport."</b><br />";
				
				echo "</div>";
				echo "<div style='width: 15%;float:left;'>".$curissuer["type"]."</div>";	
				echo "<div style='width: 15%;float:left;'>".mrod_user_get_name_from_id($mysql, $curissuer["fk_user"])."</div>";
				
				echo "<div style='width: 20%;float:left;'>";	
					echo "<a href='./?site=domains&edit=".$curissuer["id"]."'>Assign</a>";
					echo "<a href='./?site=domains&edit=".$curissuer["id"]."'>Edit</a>";
					echo "<a href='./?site=domains&delete=".$curissuer["id"]."'>Delete</a>";
				echo "</div>";	
			echo '</fieldset>';	
		}
	
	echo '</div>';
?>	
<?php if(mrod_domain_id_exists($mysql, @$_GET["edit"])) { 
		$title = "Edit Domain: ".mrod_domain_get($mysql, $_GET["edit"])["domain"]; ?>
	
	<div class="internal_popup">
		<div class="internal_popup_inner">
			<div class="internal_popup_title"><?php echo $title; ?></div>		
			<form method="post" action=""><div class="internal_popup_content">

				<input type="text" placeholder="Domain-Name" name="domain">
			<?php
						echo "<select name='fk_relay'>";
						$curissue343423	=	mysqli_query($mysql, "SELECT *	FROM "._TABLE_RELAY_." ORDER BY id DESC");				
						while ($curissuer11	=	mysqli_fetch_array($curissue343423) ) { 
							echo "<option value='".$curissuer11["id"]."'>".$curissuer11["servername"]." ".@$curissuer11["port"]."</option>";		
						} echo "</select>";			
			
			?>
				
				<input type="text" placeholder="Relay-Host" name="ovr_host">
				<input type="text" placeholder="Relay-Port" name="ovr_port">
				<input type="checkbox" name="ovr_smtps" <?php if(mrod_domain_get($mysql, $_GET["edit"])["ovr_smtps"] == 1) { echo "checked"; } ?>> Use SMTPS for Relay Settings
				<?php if(is_numeric(@$_GET["edit"])) { ?><input type="hidden" value="<?php echo @$_GET["edit"]; ?>" name="exec_ref"><?php } ?>
			</div>		
			<div class="internal_popup_submit"><input type="submit" value="Execute" name="exec_edit"><a href="./?site=dnsdomain">Cancel</a></div></form>
		</div>
	</div>
<?php } ?>
<?php if(mrod_domain_id_exists($mysql, @$_GET["assign"])) { 
		if(@$_GET["edit"] == "add") { $title = "Add new Domain"; } else { $title = "Edit Domain: ".mrod_domain_get($mysql, $_GET["edit"])["domain"]; } ?>
	
	<div class="internal_popup">
		<div class="internal_popup_inner">
			<div class="internal_popup_title"><?php echo $title; ?></div>		
			<form method="post" action=""><div class="internal_popup_content">

				<input type="text" placeholder="Domain-Name" name="domain">
			<?php
						echo "<select name='fk_relay'>";
						$curissue343423	=	mysqli_query($mysql, "SELECT *	FROM "._TABLE_USER_." ORDER BY id DESC");				
						while ($curissuer11	=	mysqli_fetch_array($curissue343423) ) { 
							echo "<option value='".$curissuer11["id"]."'>".$curissuer11["servername"]." ".@$curissuer11["port"]."</option>";		
						} echo "</select>";			
			
			?>
				
				<?php if(is_numeric(@$_GET["edit"])) { ?><input type="hidden" value="<?php echo @$_GET["edit"]; ?>" name="exec_ref"><?php } ?>
			</div>		
			<div class="internal_popup_submit"><input type="submit" value="Execute" name="exec_edit"><a href="./?site=dnsdomain">Cancel</a></div></form>
		</div>
	</div>
<?php } ?>
<?php if(mrod_domain_id_exists($mysql, @$_GET["delete"])) { ?>	
	<div class="internal_popup">
		<div class="internal_popup_inner">
			<div class="internal_popup_title">Delete: <?php echo mrod_domain_get($mysql, $_GET["edit"])["domain"]; ?></div>
			<div class="internal_popup_submit"><input type="submit" value="Execute" name="exec_del"><a href="./?site=dnsdomain">Cancel</a></div>		
		</div>
	</div>
<?php } 
	} else { echo "No Permission!"; } ?>