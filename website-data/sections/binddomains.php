<?php
	require_once("./templates/template_header.php");
	
	echo '<div class="content_box">';

		
		$curissue	=	mysqli_query($mysql->mysqlcon, "SELECT * FROM "._TABLE_DOMAIN_BIND_." ORDER BY id DESC");

		while ($curissuer	=	mysqli_fetch_array($curissue) ) { 
			echo '<fieldset><legend>'.@$curissuer["domain"].'</legend>';
				echo "<div style='width: 85%;float:left;'>";
					echo "<textarea style='background: rgba(0,0,0,0.9); color: white; width: 100%;'>".$curissuer["content"]."</textarea><br />";
				echo "</div>";
				echo "<div style='width: 15%;float:left;'>".$curissuer["modification"]."</div>";	
			echo '</fieldset>';	
		}
	
	echo '</div>';
?>	