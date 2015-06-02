<?php
include_once ULICMS_ROOT . "/lib/encryption.php";

// this class contains functions for managing user accounts
function getUsers() {
	$query = db_query ( "SELECT username FROM " . tbname ( "users" ) . " ORDER by username" );
	$users = Array ();
	while ( $row = db_fetch_object ( $query ) ) {
		array_push ( $users, $row->username );
	}
	
	return $users;
}
function getUsersOnline() {
	$users_online = db_query ( "SELECT username FROM " . tbname ( "users" ) . " WHERE last_action > " . (time () - 300) . " ORDER BY username" );
	$retval = array ();
	while ( $row = db_fetch_object ( $users_online ) ) {
		$retval [] = $row->username;
	}
	return $retval;
}
function changePassword($password, $id) {
	include_once ULICMS_ROOT . "/lib/encryption.php";
	$newPassword = hash_password ( $password );
	return db_query ( "UPDATE " . tbname ( "users" ) . " SET `password` = '$newPassword',  `old_encryption` = 0, `password_changed` = NOW() WHERE id = $id" );
}
function resetPassword($username, $length = 8) {
	$new_pass = rand_string ( $length );
	$user = getUserByName ( $username );
	if (! $user)
		return false;
	$uid = intval ( $user ["id"] );
	changePassword ( $new_pass, $uid );
	
	db_query ( "UPDATE " . tbname ( "users" ) . " SET require_password_change = 1 where id = $uid" );
	$message = TRANSLATION_RESET_PASSWORD_MAIL_BODY;
	$message = str_replace ( "%host%", get_http_host (), $message );
	$message = str_replace ( "%ip%", get_ip (), $message );
	$message = str_replace ( "%password%", $new_pass, $message );
	$message = str_replace ( "%username%", $user ["username"], $message );
	
	$headers = "From: " . getconfig ( "email" ) . "\n" . "Content-type: text/plain; charset=UTF-8";
	@ulicms_mail ( $user ["email"], TRANSLATION_RESET_PASSWORD_SUBJECT, $message, $headers );
	return true;
}
function getUserByName($name) {
	$query = db_query ( "SELECT * FROM " . tbname ( "users" ) . " WHERE username='" . db_escape ( $name ) . "'" );
	if (db_num_rows ( $query ) > 0) {
		return db_fetch_assoc ( $query );
	} else {
		return false;
	}
}
function getUserById($id) {
	$query = db_query ( "SELECT * FROM " . tbname ( "users" ) . " WHERE id = " . intval ( $id ) );
	if (db_num_rows ( $query ) > 0) {
		return db_fetch_assoc ( $query );
	} else {
		return false;
	}
}
function adduser($username, $lastname, $firstname, $email, $password, $group, $sendMessage = true, $acl_group = null, $require_password_change = 0, $admin = 0) {
	$username = db_escape ( $username );
	$lastname = db_escape ( $lastname );
	$firstname = db_escape ( $firstname );
	$email = db_escape ( $email );
	$admin = intval ( $admin );
	$password = $password;
	$require_password_change = intval ( $require_password_change );
	// legacy group
	$group = intval ( $group );
	// Default ACL Group
	if (! $acl_group)
		$acl_group = getconfig ( "default_acl_group" );
	if (! $acl_group)
		$acl_group = "NULL";
	
	if (is_null ( $acl_group ))
		$acl_group = "NULL";
	
	add_hook ( "before_create_user" );
	
	db_query ( "INSERT INTO " . tbname ( "users" ) . "
(username,lastname, firstname, email, password, `group`, `group_id`, `require_password_change`, `password_changed`, `admin`) VALUES ('$username', '$lastname','$firstname','$email','" . db_escape ( hash_password ( $password ) ) . "',$group, " . $acl_group . ", $require_password_change, NOW(), $admin)" ) or die ( db_error () );
	$message = "Hallo $firstname,\n\n" . "Ein Administrator hat auf http://" . $_SERVER ["SERVER_NAME"] . " für dich ein neues Benutzerkonto angelegt.\n\n" . "Die Zugangsdaten lauten:\n\n" . "Benutzername: $username\n" . "Passwort: $password\n";
	$header = "From: " . getconfig ( "email" ) . "\n" . "Content-type: text/plain; charset=utf-8";
	
	if ($sendMessage) {
		@ulicms_mail ( $email, "Dein Benutzer-Account bei " . $_SERVER ["SERVER_NAME"], $message, $header );
	}
	
	add_hook ( "after_create_user" );
}
function get_user_id() {
	if (isset ( $_SESSION ["login_id"] ))
		return intval ( $_SESSION ["login_id"] );
	else
		return 0;
}
function user_exists($name) {
	$query = db_query ( "SELECT id FROM " . tbname ( "users" ) . " WHERE username = '" . db_escape ( $name ) . "'" );
	return db_num_rows ( $query ) > 0;
}
function register_session($user, $redirect = true) {
	$_SESSION ["ulicms_login"] = $user ["username"];
	$_SESSION ["lastname"] = $user ["lastname"];
	$_SESSION ["firstname"] = $user ["firstname"];
	$_SESSION ["email"] = $user ["email"];
	$_SESSION ["login_id"] = $user ["id"];
	$_SESSION ["require_password_change"] = $user ["require_password_change"];
	// Soll durch group_id und eine ACL ersetzt werden
	$_SESSION ["group"] = $user ["group"];
	
	// Group ID
	$_SESSION ["group_id"] = $user ["group_id"];
	
	$_SESSION ["logged_in"] = true;
	if (is_null ( $_SESSION ["group_id"] ))
		$_SESSION ["group_id"] = 0;
	
	$_SESSION ["session_begin"] = time ();
	
	if ($user ["notify_on_login"]) {
		$subject = "Login auf \"" . getconfig ( "homepage_title" ) . "\" als " . $user ["username"];
		$text = "Von der IP " . $_SERVER ["REMOTE_ADDR"] . " hat sich jemand um " . date ( "r" ) . " erfolgreich in das Benutzerkonto " . $user ["username"] . " auf dem Server " . $_SERVER ["HTTP_HOST"] . " eingeloggt.";
		$headers = "From: " . getconfig ( "email" );
		ulicms_mail ( $user ["email"], $subject, $text, $headers );
	}
	
	if (! $redirect)
		return;
	
	if (isset ( $_REQUEST ["go"] ))
		header ( "Location: " . $_REQUEST ["go"] );
	else
		header ( "Location: index.php" );
	
	return;
}
function validate_login($user, $password) {
	include_once ULICMS_ROOT . "/lib/encryption.php";
	$user = getUserByName ( $user );
	
	if ($user) {
		if ($user ["old_encryption"])
			$password = md5 ( $password );
		else
			$password = hash_password ( $password );
		
		if ($user ["password"] == $password) {
			return $user;
		}
	}
	$_REQUEST ["error"] = TRANSLATION_USER_OR_PASSWORD_INCORRECT;
	return false;
}

?>
