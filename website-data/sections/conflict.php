<?php
	require_once("./templates/template_header.php");
	
	echo '<div class="content_box">';

		
		$curissue	=	mysqli_query($mysql->mysqlcon, "SELECT * FROM "._TABLE_CONFLICT_." ORDER BY id DESC");

		while ($curissuer	=	mysqli_fetch_array($curissue) ) { 
		
			$string = "";
			$newarray = unserialize($curissuer["servers"]);
			if(is_array($newarray)) {
				foreach($newarray AS $key => $value) {
				 $string .= "Relay ID: ".$value."<br />";
				}
			}
			echo '<fieldset><legend>'.@$curissuer["domain"].'</legend>';
				echo "<div style='width: 85%;float:left;'>";
					echo "".$string."<br />";
				echo "</div>";
				echo "<div style='width: 15%;float:left;'>".$curissuer["modification"]."</div>";	
			echo '</fieldset>';	
		}
	
	echo '</div>';
?>	