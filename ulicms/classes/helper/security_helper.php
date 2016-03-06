<?php
class securityHelper {
	// Hash Salt + Passwort with SHA1
	public static function hash_password($password) {
		$salt = Settings::get ( "password_salt" );
		
		// if no salt is set, generate it
		if (! $salt) {
			$newSalt = uniqid ();
			setconfig ( "password_salt", $newSalt );
			$salt = $newSalt;
		}
		
		return sha1 ( $salt . $password );
	}
}
