<?php
@include_once ("Mail.php");
if (! class_exists ( "Mail" ) and ! defined ( "NO_PEAR_MAIL" )) {
	define ( "NO_PEAR_MAIL", true );
}
class Mailer {
	private static function splitHeaders($headers) {
		$header_array = array ();
		$lines = explode ( "\n", $headers );
		foreach ( $lines as $line ) {
			$kv = explode ( ":", $line, 2 );
			if (! empty ( $kv [1] )) {
				$header_array [trim ( $kv [0] )] = trim ( $kv [1] );
			}
		}
		return $header_array;
	}
	private static function sendByPEAR($to, $subject, $message, $headers = "") {
		if (defined ( "NO_PEAR_MAIL" )) {
			return false;
		}
		
		$smtp_host = Settings::get ( "smtp_host" );
		if (! $smtp_host)
			$smtp_host = "127.0.0.1";
		
		$smtp_port = Settings::get ( "smtp_port" );
		if (! $smtp_port)
			$smtp_port = "25";
		
		$smtp_user = Settings::get ( "smtp_user" );
		if (! $smtp_user)
			$smtp_user = null;
		
		$smtp_password = Settings::get ( "smtp_password" );
		if (! $smtp_password)
			$smtp_password = null;
		
		if (! Settings::get ( "smtp_auth" )) {
			$mailer = Mail::factory ( 'smtp', array (
					'host' => $smtp_host,
					'port' => $smtp_port 
			) );
		} else {
			// require_authentification
			$mailer = Mail::factory ( 'smtp', array (
					'host' => $smtp_host,
					'port' => $smtp_port,
					'auth' => true,
					'username' => $smtp_user,
					'password' => $smtp_password 
			) );
		}
		
		$header_list = self::splitHeaders ( $headers );
		$header_list ['Subject'] = $subject;
		return $mailer->send ( $to, $header_list, $message );
	}
	public static function send($to, $subject, $message, $headers = "") {
		$mode = Settings::get ( "email_mode" );
		if (! $mode)
			$mode = "internal";
			
			// UliCMS speichert seit UliCMs 9.0.1 E-Mails, die das System versendet hat
			// in der Datenbank
		$insert_sql = "INSERT INTO " . tbname ( "mails" ) . " (headers, `to`, subject, body) VALUES (
     '" . db_escape ( $headers ) . "', '" . db_escape ( $to ) . "', '" . db_escape ( $subject ) . "', '" . db_escape ( $message ) . "')";
		db_query ( $insert_sql );
		
		// Damit Umlaute auch im Betreff korrekt dargestellt werden, diese mit UTF-8 kodieren
		$subject = "=?UTF-8?B?" . base64_encode ( $subject ) . "?=";
		if ($mode == "pear_mail")
			return self::sendByPEAR ( $to, $subject, $message, $headers );
		else
			return mail ( $to, $subject, $message, $headers );
	}
}