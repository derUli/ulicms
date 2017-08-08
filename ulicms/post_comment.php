<?php
include_once "init.php";

if (get_request_method () != "POST") {
	$protocol = $_SERVER ['SERVER_PROTOCOL'];
	if (! faster_in_array ( $protocol, array (
			'HTTP/1.1',
			'HTTP/2',
			'HTTP/2.0' 
	) )) {
		$protocol = 'HTTP/1.0';
	}
	header ( 'Allow: POST' );
	header ( "$protocol 405 Method Not Allowed" );
	header ( 'Content-Type: text/plain' );
	exit ();
}

add_hook ( "before_session_start" );

// initialize session
@session_start ();
$_COOKIE [session_name ()] = session_id ();

add_hook ( "after_session_start" );

setLanguageByDomain ();

$languages = getAllLanguages ();

if (! empty ( $_GET ["language"] ) and faster_in_array ( $_GET ["language"], $languages )) {
	$_SESSION ["language"] = db_escape ( $_GET ["language"] );
}

if (! isset ( $_SESSION ["language"] )) {
	$_SESSION ["language"] = Settings::get ( "default_language" );
}

setLocaleByLanguage ();

if (faster_in_array ( $_SESSION ["language"], $languages )) {
	include getLanguageFilePath ( $_SESSION ["language"] );
	Translation::loadAllModuleLanguageFiles ( $_SESSION ["language"] );
	Translation::includeCustomLangFile ( $_SESSION ["language"] );
	add_hook ( "custom_lang_" . $_SESSION ["language"] );
}

if ($_SERVER ["REQUEST_METHOD"] == "POST" and ! defined ( "NO_ANTI_CSRF" )) {
	if (! check_csrf_token ()) {
		die ( "This is probably a CSRF attack!" );
	}
}

if (Settings::get ( "check_for_spamhaus" ) and checkForSpamhaus ()) {
	$txt = get_translation ( "IP_BLOCKED_BY_SPAMHAUS" );
	$txt = str_replace ( "%ip", get_ip (), $txt );
	header ( "HTTP/1.0 403 Forbidden" );
	header ( "Content-Type: text/html; charset=UTF-8" );
	echo $txt;
	exit ();
}

// @TODO Handle Comments
