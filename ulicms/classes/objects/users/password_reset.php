<?php
class PasswordReset {
	public function addToken($user_id) {
		$token = md5 ( uniqid () . strval ( $user_id ) );
		$user_id = intval ( $user_id );
		$sql = "INSERT INTO {prefix}password_reset (token, user_id) values (?, ?)";
		$args = array (
				$token,
				$user_id 
		);
		Database::pQuery ( $sql, $args, true );
		return $token;
	}
	public function sendMail($token, $email, $ip) {
		throw new NotImplementedException ();
	}
	public function deleteToken($token) {
		$sql = "delete from {prefix}password_reset where token = ?";
		$args = array (
				strval ( $token ) 
		);
		Database::pQuery ( $sql, $args, true );
	}
}