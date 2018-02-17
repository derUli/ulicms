<?php
// this class contains functions for managing user accounts
function getUsers() {
	$query = Database::query ( "SELECT id, username FROM " . tbname ( "users" ) . " ORDER by username" );
	$users = Array ();
	while ( $row = db_fetch_object ( $query ) ) {
		array_push ( $users, $row );
	}
	
	return $users;
}

// this class contains functions for managing user accounts
function getAllUsers() {
	return getUsers ();
}
function getUsersOnline() {
	$users_online = Database::query ( "SELECT username FROM " . tbname ( "users" ) . " WHERE last_action > " . (time () - 300) . " ORDER BY username" );
	$retval = array ();
	while ( $row = db_fetch_object ( $users_online ) ) {
		$retval [] = $row->username;
	}
	return $retval;
}
function changePassword($password, $id) {
	$newPassword = Encryption::hashPassword ( $password );
	return Database::query ( "UPDATE " . tbname ( "users" ) . " SET `password` = '$newPassword',  `old_encryption` = 0, `password_changed` = NOW() WHERE id = $id" );
}
function getUserByName($name) {
	$query = Database::query ( "SELECT * FROM " . tbname ( "users" ) . " WHERE username='" . db_escape ( $name ) . "'" );
	if (db_num_rows ( $query ) > 0) {
		return db_fetch_assoc ( $query );
	} else {
		return false;
	}
}
function getUserById($id) {
	$query = Database::query ( "SELECT * FROM " . tbname ( "users" ) . " WHERE id = " . intval ( $id ) );
	if (db_num_rows ( $query ) > 0) {
		return db_fetch_assoc ( $query );
	} else {
		return false;
	}
}
function adduser($username, $lastname, $firstname, $email, $password, $sendMessage = true, $acl_group = null, $require_password_change = 0, $admin = 0, $locked = 0, $default_language = null) {
	$username = db_escape ( $username );
	$lastname = db_escape ( $lastname );
	$firstname = db_escape ( $firstname );
	$email = db_escape ( $email );
	$admin = intval ( $admin );
	$locked = intval ( $locked );
	$password = $password;
	$require_password_change = intval ( $require_password_change );
	// Default ACL Group
	if (! $acl_group) {
		$acl_group = Settings::get ( "default_acl_group" );
	}
	if (! $acl_group) {
		$acl_group = "NULL";
	}
	
	if (is_null ( $acl_group )) {
		$acl_group = "NULL";
	}
	
	add_hook ( "before_create_user" );
	
	if (StringHelper::isNullOrWhitespace ( $default_language )) {
		$default_language = "NULL";
	} else {
		$default_language = "'" . Database::escapeValue ( $default_language ) . "'";
	}
	
	Database::query ( "INSERT INTO " . tbname ( "users" ) . "
(username,lastname, firstname, email, password, `group_id`, `require_password_change`, `password_changed`, `admin`, `locked`, `default_language`) 
			VALUES ('$username', '$lastname','$firstname','$email','" . db_escape ( Encryption::hashPassword ( $password ) ) . "', " . $acl_group . ", $require_password_change, NOW(), $admin, $locked, 
			$default_language)" ) or die ( db_error () );
	$message = "Hallo $firstname,\n\n" . "Ein Administrator hat auf http://" . $_SERVER ["SERVER_NAME"] . " für dich ein neues Benutzerkonto angelegt.\n\n" . "Die Zugangsdaten lauten:\n\n" . "Benutzername: $username\n" . "Passwort: $password\n";
	$header = "From: " . Settings::get ( "email" ) . "\n" . "Content-type: text/plain; charset=utf-8";
	
	if ($sendMessage) {
		@Mailer::send ( $email, "Dein Benutzer-Account bei " . $_SERVER ["SERVER_NAME"], $message, $header );
	}
	
	add_hook ( "after_create_user" );
}
function get_user_id() {
	if (isset ( $_SESSION ["login_id"] )) {
		return intval ( $_SESSION ["login_id"] );
	} else {
		return 0;
	}
}
function user_exists($name) {
	$query = Database::query ( "SELECT id FROM " . tbname ( "users" ) . " WHERE username = '" . db_escape ( $name ) . "'" );
	return db_num_rows ( $query ) > 0;
}
function register_session($user, $redirect = true) {
	$_SESSION ["ulicms_login"] = $user ["username"];
	$_SESSION ["lastname"] = $user ["lastname"];
	$_SESSION ["firstname"] = $user ["firstname"];
	$_SESSION ["email"] = $user ["email"];
	$_SESSION ["login_id"] = $user ["id"];
	$_SESSION ["require_password_change"] = $user ["require_password_change"];
	
	// Group ID
	$_SESSION ["group_id"] = $user ["group_id"];
	
	$_SESSION ["logged_in"] = true;
	if (is_null ( $_SESSION ["group_id"] )) {
		$_SESSION ["group_id"] = 0;
	}
	
	$_SESSION ["session_begin"] = time ();
	
	Database::query ( "UPDATE " . tbname ( "users" ) . " SET `last_login` = " . time () . " where id = " . $user ["id"] );
	if ($user ["notify_on_login"]) {
		$subject = "Login auf \"" . Settings::get ( "homepage_title" ) . "\" als " . $user ["username"];
		$text = "Von der IP " . $_SERVER ["REMOTE_ADDR"] . " hat sich jemand um " . date ( "r" ) . " erfolgreich in das Benutzerkonto " . $user ["username"] . " auf dem Server " . $_SERVER ["HTTP_HOST"] . " eingeloggt.";
		$headers = "From: " . Settings::get ( "email" );
		Mailer::send ( $user ["email"], $subject, $text, $headers );
	}
	
	if (! $redirect) {
		return;
	}
	$login_url = apply_filter ( "index.php", "login_url" );
	if (isset ( $_REQUEST ["go"] )) {
		header ( "Location: " . $_REQUEST ["go"] );
	} else {
		$login_url = apply_filter ( "index.php", "login_url" );
		header ( "Location: $login_url" );
	}
	
	return;
}
function validate_login($user, $password, $token = null) {
	$user = getUserByName ( $user );
	
	if ($user) {
		if ($user ["old_encryption"]) {
			$password = md5 ( $password );
		} else {
			$password = Encryption::hashPassword ( $password );
		}
		$twofactor_authentication = Settings::get ( "twofactor_authentication" );
		if ($user ["password"] == $password) {
			if ($twofactor_authentication and ! is_null ( $token )) {
				$ga = new PHPGangsta_GoogleAuthenticator ();
				$ga_secret = Settings::get ( "ga_secret" );
				$code = $ga->getCode ( $ga_secret );
				if ($code != $token) {
					$_REQUEST ["error"] = get_translation ( "confirmation_code_wrong" );
					return false;
				}
			}
			
			if ($user ["locked"]) {
				$_REQUEST ["error"] = get_translation ( "YOUR_ACCOUNT_IS_LOCKED" );
				return false;
			}
			
			Database::query ( "update " . tbname ( "users" ) . " set `failed_logins` = 0 where id = " . intval ( $user ["id"] ) );
			return $user;
		} else {
			// Limit Login Attampts
			$max_failed_logins_items = intval ( Settings::get ( "max_failed_logins_items" ) );
			if ($max_failed_logins_items >= 1) {
				Database::query ( "update " . tbname ( "users" ) . " set `failed_logins` = `failed_logins` + 1 where id = " . intval ( $user ["id"] ) );
				Database::query ( "update " . tbname ( "users" ) . " set `locked` = 1, `failed_logins` = 0 where `failed_logins` >= $max_failed_logins_items" );
			}
		}
	}
	$_REQUEST ["error"] = get_translation ( "USER_OR_PASSWORD_INCORRECT" );
	return false;
}

// Ist der User eingeloggt
function is_logged_in() {
	return isset ( $_SESSION ["logged_in"] );
}

// Alias für is_logged_in
function logged_in() {
	return is_logged_in ();
}