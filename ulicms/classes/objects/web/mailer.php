<?php
class Mailer {
	public static function splitHeaders($headers) {
		$header_array = array ();
		$lines = normalizeLN ( $headers, "\n" );
		$lines = explode ( "\n", $lines );
		foreach ( $lines as $line ) {
			$kv = explode ( ":", $line, 2 );
			$kv = array_map ( "trim", $kv );
			$kv = array_filter ( $kv, "strlen" );
			if (count ( $kv ) == 2) {
				$header_array [trim ( $kv [0] )] = trim ( $kv [1] );
			}
		}
		return $header_array;
	}
	public static function send($to, $subject, $message, $headers = "") {
		$mode = Settings::get ( "email_mode" );
		if (! $mode) {
			$mode = EmailModes::INTERNAL;
		}
		
		// UliCMS speichert seit UliCMs 9.0.1 E-Mails, die das System versendet hat
		// in der Datenbank
		$insert_sql = "INSERT INTO " . tbname ( "mails" ) . " (headers, `to`, subject, body) VALUES ('" . db_escape ( $headers ) . "', '" . db_escape ( $to ) . "', '" . db_escape ( $subject ) . "', '" . db_escape ( $message ) . "')";
		db_query ( $insert_sql );
		
		// Damit Umlaute auch im Betreff korrekt dargestellt werden, diese mit UTF-8 kodieren
		$subject = "=?UTF-8?B?" . base64_encode ( $subject ) . "?=";
		
		// TODO: Hieraus einen Switch machen
		if ($mode == EmailModes::INTERNAL) {
			return mail ( $to, $subject, $message, $headers );
		} else {
			throw new NotImplementedException ( "E-Mail Mode \"$message\" not implemented." );
		}
	}
}