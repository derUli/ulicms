<?php
require_once "init.php";
global $connection;

add_hook ( "before_session_start" );

// initialize session
@session_start ();
$_COOKIE [session_name ()] = session_id ();

add_hook ( "after_session_start" );

setLanguageByDomain ();

$languages = getAllLanguages ();

if (! empty ( $_GET ["language"] ) and faster_in_array ( $_GET ["language"], $languages )) {
	$_SESSION ["language"] = Database::escapeValue ( $_GET ["language"], DB_TYPE_STRING );
}

if (! isset ( $_SESSION ["language"] )) {
	$_SESSION ["language"] = Settings::get ( "default_language" );
}

setLocaleByLanguage ();

require_once "templating.php";

if (faster_in_array ( $_SESSION ["language"], $languages ) && file_exists ( getLanguageFilePath ( $_SESSION ["language"] ) )) {
	include_once getLanguageFilePath ( $_SESSION ["language"] );
} else if (file_exists ( getLanguageFilePath ( "en" ) )) {
	include getLanguageFilePath ( "en" );
}

Translation::loadAllModuleLanguageFiles ( $_SESSION ["language"] );
Translation::includeCustomLangFile ( $_SESSION ["language"] );
Translation::loadCurrentThemeLanguageFiles ( $_SESSION ["language"] );
add_hook ( "custom_lang_" . $_SESSION ["language"] );

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

$status = check_status ();

if (Settings::get ( "redirection" ) != "" && Settings::get ( "redirection" ) != false) {
	add_hook ( "before_global_redirection" );
	header ( "Location: " . Settings::get ( "redirection" ) );
	exit ();
}

$theme = get_theme ();

if (strtolower ( Settings::get ( "maintenance_mode" ) ) == "on" || strtolower ( Settings::get ( "maintenance_mode" ) ) == "true" || Settings::get ( "maintenance_mode" ) == "1") {
	add_hook ( "before_maintenance_message" );
	// Sende HTTP Status 503 und Retry-After im Wartungsmodus
	header ( 'HTTP/1.0 503 Service Temporarily Unavailable' );
	header ( 'Status: 503 Service Temporarily Unavailable' );
	header ( 'Retry-After: 60' );
	header ( "Content-Type: text/html; charset=utf-8" );
	if (file_exists ( getTemplateDirPath ( $theme ) . "maintenance.php" )) {
		require_once getTemplateDirPath ( $theme ) . "maintenance.php";
	} else {
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
	Request::redirect ( $redirection, 302 );
}
try {
	$page = ContentFactory::getByID ( get_ID () );
	if (! is_null ( $page->id ) and $page instanceof Language_Link) {
		$language = new Language ( $page->link_to_language );
		if (! is_null ( $language->getID () ) and StringHelper::isNotNullOrWhitespace ( $language->getLanguageLink () )) {
			Request::redirect ( $language->getLanguageLink () );
		}
	}
} catch ( Exception $e ) {
}

if (isset ( $_GET ["goid"] )) {
	$goid = intval ( $_GET ["goid"] );
	$url = ModuleHelper::getFullPageURLByID ( $goid );
	if ($url) {
		Request::redirect ( $url, 301 );
	} else {
		$url = getBaseFolderURL ();
		Request::redirect ( $url, 301 );
	}
}

if (isset ( $_GET ["submit-cms-form"] ) and ! empty ( $_GET ["submit-cms-form"] ) and get_request_method () === "POST") {
	$form_id = intval ( $_GET ["submit-cms-form"] );
	
	require_once ULICMS_ROOT . "/classes/objects/content/forms.php";
	Forms::submitForm ( $form_id );
}
ControllerRegistry::runMethods ();

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

if (count ( getThemeList () ) === 0) {
	throw new Exception ( "Keine Themes vorhanden!" );
}

if (! is_dir ( getTemplateDirPath ( $theme ) )) {
	throw new Exception ( "Das aktivierte Theme existiert nicht!" );
}

if (file_exists ( getTemplateDirPath ( $theme ) . "functions.php" )) {
	include getTemplateDirPath ( $theme ) . "functions.php";
}

$cached_page_path = buildCacheFilePath ( $_SERVER ['REQUEST_URI'] );
$hasModul = containsModule ( get_requested_pagename () );

$cache_control = get_cache_control ();
switch ($cache_control) {
	case "auto" :
	case "force" :
		Flags::setNoCache ( false );
		break;
		break;
	case "no_cache" :
		Flags::setNoCache ( true );
		break;
}
if ($hasModul) {
	no_cache ();
}

// Kein Caching wenn man eingeloggt ist
if (is_logged_in () and get_cache_control () == "auto") {
	no_cache ();
}

add_hook ( "before_html" );

$c = Settings::get ( "cache_type" );
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

if (file_exists ( $cached_page_path ) and ! Settings::get ( "cache_disabled" ) and getenv ( 'REQUEST_METHOD' ) == "GET" and $cache_type === "file") {
	$cached_content = file_get_contents ( $cached_page_path );
	$last_modified = filemtime ( $cached_page_path );
	
	if ($cached_content and (time () - $last_modified < CACHE_PERIOD) and ! Flags::getNoCache ()) {
		eTagFromString ( $cached_content );
		browsercacheOneDay ( $last_modified );
		echo $cached_content;
		
		if (Settings::get ( "no_auto_cron" )) {
			die ();
		}
		
		add_hook ( "before_cron" );
		@include 'cron.php';
		add_hook ( "after_cron" );
		die ();
	}
}

if (! Settings::get ( "cache_disabled" ) and getenv ( 'REQUEST_METHOD' ) == "GET" and ! file_exists ( $cached_page_path ) and $cache_type === "file") {
	ob_start ();
} else if (file_exists ( $cached_page_path )) {
	$last_modified = filemtime ( $cached_page_path );
	if (time () - $last_modified < CACHE_PERIOD) {
		ob_start ();
	}
}

$id = md5 ( $_SERVER ['REQUEST_URI'] . $_SESSION ["language"] . strbool ( is_mobile () ) );

if (! Settings::get ( "cache_disabled" ) and ! Flags::getNoCache () and getenv ( 'REQUEST_METHOD' ) == "GET" and $cache_type === "cache_lite") {
	$options = array (
			'lifeTime' => Settings::get ( "cache_period" ),
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
	if (file_exists ( $html_file )) {
		echo file_get_contents ( $html_file );
	} else {
		echo "File Not Found";
	}
} else {
	$top_files = array (
			"type/" . get_type () . "/oben.php",
			"type/" . get_type () . "/top.php",
			"oben.php",
			"top.php" 
	);
	foreach ( $top_files as $file ) {
		$file = getTemplateDirPath ( $theme ) . $file;
		if (file_exists ( $file )) {
			require $file;
			break;
		}
	}
	add_hook ( "before_content" );
	$text_position = get_text_position ();
	
	if ($text_position == "after") {
		Template::outputContentElement ();
	}
	
	content ();
	
	if ($text_position == "before") {
		Template::outputContentElement ();
	}
	
	add_hook ( "after_content" );
	
	add_hook ( "before_edit_button" );
	
	edit_button ();
	add_hook ( "after_edit_button" );
	$bottom_files = array (
			"type/" . get_type () . "/unten.php",
			"type/" . get_type () . "/bottom.php",
			"unten.php",
			"bottom.php" 
	);
	foreach ( $bottom_files as $file ) {
		$file = getTemplateDirPath ( $theme ) . $file;
		if (file_exists ( $file )) {
			require $file;
			break;
		}
	}
}

add_hook ( "after_html" );

if (! Settings::get ( "cache_disabled" ) and ! Flags::getNoCache () and $cache_type === "cache_lite") {
	$data = ob_get_clean ();
	
	if (! defined ( "EXCEPTION_OCCURRED" ) and ! Flags::getNoCache ()) {
		$Cache_Lite->save ( $data, $id );
	}
	
	eTagFromString ( $data );
	browsercacheOneDay ();
	echo $data;
	
	if (Settings::get ( "no_auto_cron" )) {
		die ();
	}
	add_hook ( "before_cron" );
	@include 'cron.php';
	add_hook ( "after_cron" );
	die ();
}

if (! Settings::get ( "cache_disabled" ) and ! Flags::getNoCache () and getenv ( 'REQUEST_METHOD' ) == "GET" and $cache_type === "file") {
	$generated_html = ob_get_clean ();
	
	if (! defined ( "EXCEPTION_OCCURRED" ) and ! Flags::getNoCache ()) {
		$handle = fopen ( $cached_page_path, "wb" );
		fwrite ( $handle, $generated_html );
		fclose ( $handle );
	}
	
	eTagFromString ( $generated_html );
	browsercacheOneDay ();
	echo ($generated_html);
	
	// Wenn no_auto_cron gesetzt ist, dann muss cron.php manuell ausgeführt bzw. aufgerufen werden
	if (Settings::get ( "no_auto_cron" )) {
		die ();
	}
	add_hook ( "before_cron" );
	@include 'cron.php';
	add_hook ( "after_cron" );
	die ();
} else {
	if (Settings::get ( "no_auto_cron" )) {
		die ();
	}
	add_hook ( "before_cron" );
	@include 'cron.php';
	add_hook ( "after_cron" );
	die ();
}
