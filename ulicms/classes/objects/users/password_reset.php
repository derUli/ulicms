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
	public function sendMail($token, $to, $ip, $firstname, $lastname) {
		ViewBag::set ( "url", $this->getPasswordResetLink ( $token ) );
		ViewBag::set ( "firstname", $firstname );
		ViewBag::set ( "lastname", $lastname );
		ViewBag::set ( "ip", $ip );

		$message = Template::executeDefaultOrOwnTemplate ( "email/password_reset" );
		$subject = get_translation ( "reset_password_subject" );
		$from = Settings::get ( "email" );
		$headers = "From: $from\n";
		$headers .= "Content-type: text/plain; charset=utf-8";
		Mailer::send ( $to, $subject, $message, $headers );
	}
	public function getPasswordResetLink($token) {
		$url = getBaseFolderURL ();
		$url = rtrim ( $url, "/" );
		if (! is_admin_dir ()) {
			$url .= "/admin";
		}
		$url .= "/?reset_password_token=" . $token;
		return $url;
	}
	public function getToken($token) {
		$sql = "select * from {prefix}password_reset where token = ?";
		$args = array (
				strval ( $token )
		);
		$query = Database::pQuery ( $sql, $args, true );
		if (Database::any ( $query )) {
			return Database::fetchObject ( $query );
		} else {
			return null;
		}
	}
	public function deleteToken($token) {
		$sql = "delete from {prefix}password_reset where token = ?";
		$args = array (
				strval ( $token )
		);
		Database::pQuery ( $sql, $args, true );
	}
}
