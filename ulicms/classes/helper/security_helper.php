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
	public static function securePath($path) {
		$securedPath = array ();
		$path = explode ( "/", $path );
		foreach ( $path as $key => $value ) {
			if ($value != "." and $value != "..") {
				$securedPath [] = $value;
			}
		}
		$securedPath = array_map ( 'trim', $securedPath );
		$securedPath = array_filter ( $securedPath, 'strlen' );
		$securedPath = array_filter ( $securedPath, 'is_null' );
		$securedPath = "/" . implode ( "/", $securedPath );
		return $securedPath;
	}
}
