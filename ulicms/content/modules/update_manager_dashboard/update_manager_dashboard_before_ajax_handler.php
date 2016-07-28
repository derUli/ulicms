<?php
if (isset ( $_REQUEST ["ajax_cmd"] ) and $_REQUEST ["ajax_cmd"] == "anyUpdateAvailable") {
	
	include_once getModulePath ( "update_manager_dashboard" ) . "/objects/update_manager_dashboard.php";
	if (UpdateManagerDashboard::anyUpdateAvailable ()) {
		echo "yes";
	} else {
		echo "no";
	}
	die ();
}