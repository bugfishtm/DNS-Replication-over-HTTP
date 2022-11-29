<?php
	// Configurations Include
		require_once(dirname(__FILE__) ."/../settings.php");

	// Delete IP Blacklist Table Entries 
		$mysql->query("DELETE FROM "._TABLE_IPBL_." ");
?>
