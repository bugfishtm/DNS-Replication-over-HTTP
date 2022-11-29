<?php
if(!$permsobj->hasPerm($user->user_id, "perm_admin") AND $user->user_rank != 0) { echo "You do not have Permission!"; } else {	
	require_once("./templates/template_header.php");
	
	echo '<div class="content_box">';
	echo '<fieldset><legend>IP Blocklist</legend>';
		
		$curissue	=	mysqli_query($mysql->mysqlcon, "SELECT *	FROM "._TABLE_IPBL_."  ORDER BY id DESC"); 

		while ($curissuer	=	mysqli_fetch_array($curissue) ) { 
			if($curissuer["smtps"] != 1) { $finalcon =  "smtp:"; } else { $finalcon =  "smtps:"; }
			echo "<div>";
				echo "<div style='width: 70%;float:left;'>".$curissuer["ip_adr"]."</div>";
				echo "<div style='width: 30%;float:left;'>".$curissuer["failures"]."</div>";

			echo '</div><br clear="left"/>';	
		}
	
echo '</fieldset></div>'; }
?>	