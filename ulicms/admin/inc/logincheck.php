<?php
if (isset ( $_GET ["destroy"] ) or $_GET ["action"] == "destroy") {
	db_query ( "UPDATE " . tbname ( "users" ) . " SET last_action = 0 WHERE id = " . $_SESSION ["login_id"] );
	$url = apply_filter ( "index.php", "logout_url" );
	header ( "Location: $url" );
	session_destroy ();
	exit ();
}

if (isset ( $_REQUEST ["reset_password_token"] )) {
	$reset = new PasswordReset ();
	$token = $reset->getToken ( $_REQUEST ["reset_password_token"] );
	if ($token) {
		$user_id = $token->user_id;
		$user = new User ( $user_id );
		$user->setRequirePasswordChange ( 1 );
		$user->save ();
		register_session ( getUserById ( $user_id ) );
		$token = $reset->deleteToken ( $_REQUEST ["reset_password_token"] );
	} else {
		// @FIXME Fehler anzeigen "Token ungültig"
	}
}

if (isset ( $_POST ["login"] )) {
	if (StringHelper::isNotNullOrWhitespace ( $_POST ["system_language"] )) {
		$_SESSION ["system_language"] = basename ( $_POST ["system_language"] );
	} else {
		$user = new User ();
		$user->loadByUsername ( $_POST ["user"] );
		$_SESSION ["system_language"] = $user->getDefaultLanguage () ? $user->getDefaultLanguage () : Settings::get ( "system_language" );
	}
	
	$confirmation_code = null;
	$twofactor_authentication = Settings::get ( "twofactor_authentication" );
	
	if ($twofactor_authentication) {
		// @TODO: Confirmation Code nur Prüfen, wenn 2-Faktor Authentifizerung aktiviert ist
		$confirmation_code = $_POST ["confirmation_code"];
	}
	
	$sessionData = validate_login ( $_POST ["user"], $_POST ["password"], $confirmation_code );
	if ($sessionData) {
		add_hook ( "login_ok" );
		register_session ( $sessionData, true );
	} else {
		@header ( 'HTTP/1.0 403 Forbidden' );
		add_hook ( "login_failed" );
	}
}

?>
