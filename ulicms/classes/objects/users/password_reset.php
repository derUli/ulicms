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
}