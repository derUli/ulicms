<?php
if (isset ( $_GET ["destroy"] ) or $_GET ["action"] == "destroy") {
	db_query ( "UPDATE " . tbname ( "users" ) . " SET last_action = 0 WHERE id = " . $_SESSION ["login_id"] );
	header ( "Location: index.php" );
	
	session_destroy ();
	exit ();
}

if (isset ( $_POST ["login"] )) {
	
	if (isset ( $_POST ["system_language"] )) {
		$_SESSION ["system_language"] = basename ( $_POST ["system_language"] );
	}
	
	$confirmation_code = null;
	// @TODO: Confirmation Code nur Prüfen, wenn 2-Faktor Authentifizerung aktiviert ist
	$confirmation_code = $_POST["confirmation_code"];
	
	$sessionData = validate_login ( $_POST ["user"], $_POST ["password"], $confirmation_code);
	if ($sessionData) {
		add_hook ( "login_ok" );
		register_session ( $sessionData, true );
	} else {
		add_hook ( "login_failed" );
	}
}

?>
