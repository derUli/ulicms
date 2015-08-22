<?php
require_once "init.php";
global $connection;

add_hook ( "before_session_start" );

// initialize session
@session_start ();
$_COOKIE [session_name ()] = session_id ();

add_hook ( "after_session_start" );

setLanguageByDomain ();

if (! empty ( $_GET ["language"] ) and in_array ( $_GET ["language"], getAllLanguages () )) {
	$_SESSION ["language"] = db_escape ( $_GET ["language"] );
}

if (! isset ( $_SESSION ["language"] )) {
	$_SESSION ["language"] = getconfig ( "default_language" );
}

setLocaleByLanguage ();

if (in_array ( $_SESSION ["language"], getAllLanguages () )) {
	include getLanguageFilePath ( $_SESSION ["language"] );
}

if ($_SERVER ["REQUEST_METHOD"] == "POST" and ! defined ( "NO_ANTI_CSRF" )) {
	if (! check_csrf_token ()) {
		die ( "This is probably a CSRF attack!" );
	}
}

if (getconfig ( "check_for_spamhaus" ) and checkForSpamhaus ()) {
	$txt = get_translation ( "IP_BLOCKED_BY_SPAMHAUS" );
	
	$txt = str_replace ( "%ip", get_ip (), $txt );
	header ( "HTTP/1.0 403 Forbidden" );
	header ( "Content-Type: text/html; charset=UTF-8" );
	echo $txt;
	exit ();
}
require_once "templating.php";
$status = check_status ();

if (getconfig ( "redirection" ) != "" && getconfig ( "redirection" ) != false) {
	add_hook ( "before_global_redirection" );
	header ( "Location: " . getconfig ( "redirection" ) );
	exit ();
}

$theme = get_theme ();

if (strtolower ( getconfig ( "maintenance_mode" ) ) == "on" || strtolower ( getconfig ( "maintenance_mode" ) ) == "true" || getconfig ( "maintenance_mode" ) == "1") {
	add_hook ( "before_maintenance_message" );
	// Sende HTTP Status 503 und Retry-After im Wartungsmodus
	header ( 'HTTP/1.0 503 Service Temporarily Unavailable' );
	header ( 'Status: 503 Service Temporarily Unavailable' );
	header ( 'Retry-After: 60' );
	header ( "Content-Type: text/html; charset=utf-8" );
	if (file_exists ( getTemplateDirPath ( $theme ) . "maintenance.php" )){
  		require_once getTemplateDirPath ( $theme ) . "maintenance.php";
	}	
	else {
 		die ( get_translation ( "UNDER_MAINTENANCE" ) );
	}
	add_hook ( "after_maintenance_message" );
	die ();
}

if (isset ( $_GET ["format"] ) and ! empty ( $_GET ["format"] )) {
	$format = trim ( $_GET ["format"] );
} else {
	$format = "html";
}

add_hook ( "before_http_header" );

$redirection = get_redirection ();

if ($redirection) {
	ulicms_redirect ( $redirection, 302 );
}

header ( "HTTP/1.0 " . $status );

if ($format == "html") {
	header ( "Content-Type: text/html; charset=utf-8" );
} else if ($format == "pdf") {
	$pdf = new PDFCreator ();
	$pdf->output ();
} else if ($format == "csv") {
	$csv = new CSVCreator ();
	$csv->output ();
} else if ($format == "json") {
	$json = new JSONCreator ();
	$json->output ();
} else if ($format == "txt") {
	$plain = new PlainTextCreator ();
	$plain->output ();
} else {
	$format = "html";
}

add_hook ( "after_http_header" );

if (count ( getThemeList () ) === 0)
	throw new Exception ( "Keine Themes vorhanden!" );

if (! is_dir ( getTemplateDirPath ( $theme ) ))
	throw new Exception ( "Das aktivierte Theme existiert nicht!" );

if (file_exists ( getTemplateDirPath ( $theme ) . "functions.php" )) {
	include getTemplateDirPath ( $theme ) . "functions.php";
}

$cached_page_path = buildCacheFilePath ( $_SERVER ['REQUEST_URI'] );
$modules = getAllModules ();
$hasModul = containsModule ( get_requested_pagename () );

// Kein Caching wenn man eingeloggt ist
if (is_logged_in ()) {
	no_cache ();
}

add_hook ( "before_html" );

$c = getconfig ( "cache_type" );
switch ($c) {
	case "cache_lite" :
		@include "Cache/Lite.php";
		$cache_type = "cache_lite";
		
		break;
	case "file" :
	default :
		$cache_type = "file";
		break;
		break;
}

if (file_exists ( $cached_page_path ) and ! getconfig ( "cache_disabled" ) and getenv ( 'REQUEST_METHOD' ) == "GET" and $cache_type === "file") {
	
	$cached_content = file_get_contents ( $cached_page_path );
	$last_modified = filemtime ( $cached_page_path );
	
	if ($cached_content and (time () - $last_modified < CACHE_PERIOD) and ! defined ( "NO_CACHE" )) {
		eTagFromString ( $cached_content );
		browsercacheOneDay ( $last_modified );
		echo $cached_content;
		
		if (getconfig ( "no_auto_cron" ))
			die ();
		
		add_hook ( "before_cron" );
		@include 'cron.php';
		add_hook ( "after_cron" );
		die ();
	}
}

if (! getconfig ( "cache_disabled" and getenv ( 'REQUEST_METHOD' ) == "GET" ) and ! file_exists ( $cached_page_path ) and $cache_type === "file") {
	ob_start ();
} else if (file_exists ( $cached_page_path )) {
	$last_modified = filemtime ( $cached_page_path );
	if (time () - $last_modified < CACHE_PERIOD) {
		ob_start ();
	}
}

$id = md5 ( $_SERVER ['REQUEST_URI'] . $_SESSION ["language"] . strbool ( is_mobile () ) );

if (! getconfig ( "cache_disabled" ) and ! $hasModul and getenv ( 'REQUEST_METHOD' ) == "GET" and $cache_type === "cache_lite") {
	$options = array (
			'lifeTime' => getconfig ( "cache_period" ),
			'cacheDir' => "content/cache/" 
	);
	
	if (! class_exists ( "Cache_Lite" )) {
		throw new Exception ( "Fehler:<br/>Cache_Lite ist nicht installiert. Bitte stellen Sie den Cache bitte wieder auf Datei-Modus um." );
	}
	$Cache_Lite = new Cache_Lite ( $options );
	
	if ($data = $Cache_Lite->get ( $id )) {
		die ( $data );
	} else {
		ob_start ();
	}
}

$html_file = page_has_html_file ( get_requested_pagename () );

if ($html_file) {
	if (file_exists ( $html_file ))
		echo file_get_contents ( $html_file );
	else
		echo "File Not Found";
} else {
	require_once getTemplateDirPath ( $theme ) . "oben.php";
	add_hook ( "before_content" );
	content ();
	
	edit_button ();
	add_hook ( "after_content" );
	
	require_once getTemplateDirPath ( $theme ) . "unten.php";
}

add_hook ( "after_html" );

if (! getconfig ( "cache_disabled" ) and ! $hasModul and getenv ( 'REQUEST_METHOD' ) == "GET" and $cache_type === "cache_lite") {
	$data = ob_get_clean ();
	
	if (! defined ( "EXCEPTION_OCCURRED" ) and ! defined ( "NO_CACHE" )) {
		$Cache_Lite->save ( $data, $id );
	}
	
	eTagFromString ( $data );
	browsercacheOneDay ();
	echo $data;
	
	if (getconfig ( "no_auto_cron" ))
		die ();
	add_hook ( "before_cron" );
	@include 'cron.php';
	add_hook ( "after_cron" );
	die ();
}

if (! getconfig ( "cache_disabled" ) and ! $hasModul and getenv ( 'REQUEST_METHOD' ) == "GET" and $cache_type === "file") {
	$generated_html = ob_get_clean ();
	
	if (! defined ( "EXCEPTION_OCCURRED" ) and ! defined ( "NO_CACHE" )) {
		$handle = fopen ( $cached_page_path, "wb" );
		fwrite ( $handle, $generated_html );
		fclose ( $handle );
	}
	
	eTagFromString ( $generated_html );
	browsercacheOneDay ();
	echo ($generated_html);
	
	// Wenn no_auto_cron gesetzt ist, dann muss cron.php manuell ausgeführt bzw. aufgerufen werden
	if (getconfig ( "no_auto_cron" ))
		die ();
	
	add_hook ( "before_cron" );
	@include 'cron.php';
	add_hook ( "after_cron" );
	die ();
} else {
	
	if (getconfig ( "no_auto_cron" ))
		die ();
	
	add_hook ( "before_cron" );
	@include 'cron.php';
	add_hook ( "after_cron" );
	die ();
}
