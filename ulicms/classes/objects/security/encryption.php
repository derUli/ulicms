<?php
class Encryption {
	public static function hashPassword($password) {
		$salt = Settings::get ( "password_salt" );
		
		// if no salt is set, generate it
		if (! $salt) {
			$newSalt = uniqid ();
			Settings::set ( "password_salt", $newSalt );
			$salt = $newSalt;
		}
		return hash ( "sha512", $salt . $password );
	}
}

?>