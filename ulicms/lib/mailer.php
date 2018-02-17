<?php
function split_headers($headers) {
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

function ulicms_mail($to, $subject, $message, $headers = "") {
	$mode = Settings::get ( "email_mode" );
	if (! $mode) {
		$mode = EmailModes::INTERNAL;
	}
	
	// UliCMS speichert seit UliCMs 9.0.1 E-Mails, die das System versendet hat
	// in der Datenbank
	$insert_sql = "INSERT INTO " . tbname ( "mails" ) . " (headers, `to`, subject, body) VALUES (
     '" . db_escape ( $headers ) . "', '" . db_escape ( $to ) . "', '" . db_escape ( $subject ) . "', '" . db_escape ( $message ) . "')";
	db_query ( $insert_sql );
	
	// Damit Umlaute auch im Betreff korrekt dargestellt werden, diese mit UTF-8 kodieren
	$subject = "=?UTF-8?B?" . base64_encode ( $subject ) . "?=";
	
	return mail ( $to, $subject, $message, $headers );
}
