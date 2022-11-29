<?php
if(isset($_POST["exec_edit"])) {
	if(trim(@$_POST["path"]) != "" AND trim(@$_POST["token"]) != "" AND trim(@$_POST["ip"]) != "") {
		if(is_numeric(@$_POST["exec_ref"])) {
			$mysql->query("UPDATE "._TABLE_SERVER_." SET api_path = '".$mysql->escape(trim($_POST["path"]))."' WHERE id = \"".$_POST["exec_ref"]."\";");
			$mysql->query("UPDATE "._TABLE_SERVER_." SET api_token = '".$mysql->escape(trim($_POST["token"]))."' WHERE id = \"".$_POST["exec_ref"]."\";");
			$mysql->query("UPDATE "._TABLE_SERVER_." SET ip = '".$mysql->escape(trim($_POST["ip"]))."' WHERE id = \"".$_POST["exec_ref"]."\";");
			if($_POST["master"]) { $master = 1; } else  { $master = 0 ;}
			$mysql->query("UPDATE "._TABLE_SERVER_." SET server_type = '".$master."' WHERE id = \"".$_POST["exec_ref"]."\";");
			x_eventBoxPrep("Relay has been updated!", "ok", _COOKIES_);	
		} else {											
			if($_POST["master"]) { $master = 1; } else  { $master = 0 ;}
			$mysql->query("INSERT INTO "._TABLE_SERVER_." (api_path, api_token, server_type, fk_user, ip) 
														VALUES (\"".$mysql->escape(trim($_POST["path"]))."\"
														, '".$mysql->escape(trim($_POST["token"]))."'
														, '".$master."'
														, '".$user->user_id."'
														, '".$user->user_id."');");
			x_eventBoxPrep("Relay has been added!", "ok", _COOKIES_);
		}
	} else { x_eventBoxPrep("Error in submitted data!", "error", _COOKIES_);  }
}

if(isset($_POST["exec_del"]) AND dnshttp_server_id_exists($mysql, @$_POST["exec_ref"])) {
	if(is_numeric($_POST["exec_ref"])) {
		$res = $mysql->select("SELECT * FROM "._TABLE_DOMAIN_." WHERE fk_relay = '".$_POST["exec_ref"]."'", false);
		if(!is_array($res)) {
			$mysql->query("DELETE FROM `"._TABLE_SERVER_."` WHERE id = \"".$_POST["exec_ref"]."\";");
			$mysql->query("DELETE FROM `"._TABLE_DOMAIN_API_."` WHERE fk_server = \"".$_POST["exec_ref"]."\";");
			x_eventBoxPrep("Relay has been deleted!", "ok", _COOKIES_);
		} else { x_eventBoxPrep("Relay is in use for different Domains!", "error", _COOKIES_); }
	} 
}
	
	require_once("./templates/template_header.php");
	
	echo '<div class="content_box"><a href="./?site=server&edit=add">Add new Server</a>';
		
		$curissue	=	mysqli_query($mysql->mysqlcon, "SELECT *	FROM "._TABLE_SERVER_."  ORDER BY id DESC");

		while ($curissuer	=	mysqli_fetch_array($curissue) ) { 
			if($curissuer["server_type"] != 1) { $server_type =  "Slave"; } else { $server_type =  "Master"; }
			echo '<fieldset><legend>Server-ID: '.@$curissuer["id"].'</legend>';
				echo "<div style='width: 60%;float:left;'>";
					echo "<b>Path: ".@$curissuer["api_path"]."</b><br />";
					echo "Token: ".@$curissuer["api_token"]."<br />";
					echo "IP: ".@$curissuer["ip"]."<br />";
					echo "Type: ".@$server_type."<br />";
					echo "Owner: ".dnshttp_user_get_name_from_id($mysql, @$curissuer["fk_user"])."<br />";
				echo "</div>";	
				
				echo "<div style='width: 20%;float:left;'>";	
					echo "<div style='float:left;'><a href='./?site=server&testc=".$curissuer["id"]."'>Get-Con</a></div>";
					echo "<div style='float:left;'><a href='./?site=server&testt=".$curissuer["id"]."'>Get-SecCon</a></div>";
					echo "<div style='float:left;'><a href='./?site=server&testd=".$curissuer["id"]."'>Get-Domains</a></div>";
					echo "<div style='float:left;'><a href='./?site=server&edit=".$curissuer["id"]."'>Edit</a></div>";
					echo "<div style='float:left;'><a href='./?site=server&delete=".$curissuer["id"]."'>Delete</a></div>";
				echo "</div>";	
			echo '</fieldset>';	
		}
	
	echo '</div>';
?>	
<?php if(dnshttp_server_id_exists($mysql, @$_GET["edit"]) OR @$_GET["edit"] == "add") { 
		if(@$_GET["edit"] == "add") { $title = "Add new Server"; } else { $title = "Edit Relay: ".dnshttp_server_get($mysql, $_GET["edit"])["id"]; } ?>
	
	<div class="internal_popup">
		<div class="internal_popup_inner">
			<div class="internal_popup_title"><?php echo $title; ?></div>		
			<form method="post" action="./?site=server"><div class="internal_popup_content">			
				<input type="text" placeholder="Api-Path" name="path" value="<?php echo dnshttp_server_get($mysql, $_GET["edit"])["api_path"]; ?>">
				<input type="text" placeholder="Api-Token" name="token" value="<?php echo dnshttp_server_get($mysql, $_GET["edit"])["api_token"]; ?>">
				<input type="text" placeholder="Server-IP" name="ip" value="<?php echo dnshttp_server_get($mysql, $_GET["edit"])["ip"]; ?>">
				<input type="checkbox" name="slave" <?php if(dnshttp_server_get($mysql, $_GET["edit"])["server_type"] != 1) { echo "checked"; } ?>>Slave DNS-Server
				<input type="checkbox" name="master" <?php if(dnshttp_server_get($mysql, $_GET["edit"])["server_type"] == 1) { echo "checked"; } ?>>Master DNS-Server
				<?php if(is_numeric(@$_GET["edit"])) { ?><input type="hidden" value="<?php echo @$_GET["edit"]; ?>" name="exec_ref"><?php } ?>
			</div>		
			<div class="internal_popup_submit"><input type="submit" value="Execute" name="exec_edit"><a href="./?site=server">Cancel</a></div></form>
		</div>
	</div>
<?php } ?>
<?php if(dnshttp_server_id_exists($mysql, @$_GET["delete"])) { ?>	
	<div class="internal_popup">
		<form method="post" action="./?site=server"><div class="internal_popup_inner">
			<div class="internal_popup_title">Delete: <?php echo dnshttp_server_get($mysql, $_GET["delete"])["id"]; ?></div>
			<div class="internal_popup_submit"><input type="hidden" value="<?php echo @$_GET["delete"]; ?>" name="exec_ref"><input type="submit" value="Execute" name="exec_del"><a href="./?site=server">Cancel</a></div>		
		</div></form>
	</div>
<?php } ?>
<?php if(dnshttp_server_id_exists($mysql, @$_GET["testc"])) { 
		$apipath	=	dnshttp_server_get($mysql, $_GET["testc"])["api_path"]."/api/status.php";
		$returncurl =   dnshttp_api_getcontent($mysql, $apipath);
		if($returncurl == "online") { $output 	=	"SUCCESS"; } else { $output 	=	"ERROR"; }
		if($returncurl == "tokenerror") { $output 	=	"TOKEN ERROR"; } 
			
?>	
	<div class="internal_popup">
		<div class="internal_popup_inner">
			<div class="internal_popup_title">Test: <?php echo dnshttp_server_get($mysql, $_GET["testc"])["id"]; ?></div>
			<div class="internal_popup_content">Path: <?php echo $apipath; ?> <br /> Output: <?php echo $output; ?> </div>
			<div class="internal_popup_submit"><a href="./?site=server">Cancel</a></div>		
		</div>
	</div>
<?php } ?>
<?php if(dnshttp_server_id_exists($mysql, @$_GET["testt"])) { 
		$apipath	=	dnshttp_server_get($mysql, $_GET["testt"])["api_path"]."/api/status_token.php";
		$returncurl =   dnshttp_api_getcontent($mysql, $apipath, dnshttp_server_get($mysql, $_GET["testt"])["api_token"]);
		if($returncurl == "online") { $output 	=	"SUCCESS"; } else { $output 	=	"ERROR"; }
?>	
	<div class="internal_popup">
		<div class="internal_popup_inner">
			<div class="internal_popup_title">Test: <?php echo dnshttp_server_get($mysql, $_GET["testt"])["id"]; ?></div>
			<div class="internal_popup_content">Path: <?php echo $apipath; ?> <br /> Output: <?php echo $output; ?> </div>
			<div class="internal_popup_submit"><a href="./?site=server">Cancel</a></div>		
		</div>
	</div>
<?php } ?>
<?php if(dnshttp_server_id_exists($mysql, @$_GET["testd"])) { 
		$apipath	=	dnshttp_server_get($mysql, $_GET["testd"])["api_path"]."/api/master_domain_list.php";
		$returncurl =   dnshttp_api_getcontent($mysql, $apipath, dnshttp_server_get($mysql, $_GET["testd"])["api_token"]);
		if(is_string($returncurl)) { $output 	=	"SUCCESS<br /><a href='".$apipath."?token=".dnshttp_server_get($mysql, $_GET["testd"])["api_token"]."' target='_blank'>View in Tab</a>"; } else { $output 	=	"ERROR<br /><a href='".$apipath."?token=".dnshttp_server_get($mysql, $_GET["testd"])["api_token"]."' target='_blank'>View in Tab</a>"; }
?>	
	<div class="internal_popup">
		<div class="internal_popup_inner">
			<div class="internal_popup_title">Test: <?php echo dnshttp_server_get($mysql, $_GET["testd"])["id"]; ?></div>
			<div class="internal_popup_content">Path: <?php echo $apipath; ?> <br /> Output: <?php echo $output; ?> </div>
			<div class="internal_popup_submit"><a href="./?site=server">Cancel</a></div>		
		</div>
	</div>
<?php } ?>