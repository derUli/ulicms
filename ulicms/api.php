<?php
function is_numeric_array($var) {
	if (! is_array ( $var )) {
		return false;
	}
	foreach ( $var as $key => $value ) {
		if (! is_numeric ( $value )) {
			return false;
		}
	}
	return true;
}
function var_is_type($var, $type, $required = false) {
	$methodName = "is_{$type}";
	
	if ($var === null or $var === "") {
		return ! $required;
	}
	
	if (function_exists ( $methodName )) {
		return $methodName ( $var );
	}
	return false;
}
function var_dump_str() {
	$argc = func_num_args ();
	$argv = func_get_args ();
	
	if ($argc > 0) {
		ob_start ();
		call_user_func_array ( 'var_dump', $argv );
		$result = ob_get_contents ();
		ob_end_clean ();
		return $result;
	}
	
	return '';
}
function remove_prefix($text, $prefix) {
	if (0 === strpos ( $text, $prefix ))
		$text = substr ( $text, strlen ( $prefix ) );
	return $text;
}

// replacement for the each() function which is deprecated since PHP 7.2.0
function myEach(&$arr) {
	$key = key ( $arr );
	$result = ($key === null) ? false : [ 
			$key,
			current ( $arr ),
			'key' => $key,
			'value' => current ( $arr ) 
	];
	next ( $arr );
	return $result;
}

if (! function_exists ( "each" )) {
	function each(&$arr) {
		return myEach ( $arr );
	}
}
function is_true($var) {
	return (isset ( $var ) and $var);
}
function is_false($var) {
	return ! (isset ( $var ) and $var);
}
function bool2YesNo($value, $yesString = null, $noString = null) {
	if (! $yesString) {
		$yesString = get_translation ( "yes" );
	}
	if (! $noString) {
		$noString = get_translation ( "no" );
	}
	return ($value ? $yesString : $noString);
}

// like json_encode() but human readable
function json_readable_encode($in, $indent = 0) {
	$_myself = __FUNCTION__;
	$_escape = function ($str) {
		return preg_replace ( "!([\b\t\n\r\f\"\\'])!", "\\\\\\1", $str );
	};
	
	$out = '';
	
	foreach ( $in as $key => $value ) {
		$out .= str_repeat ( "\t", $indent + 1 );
		$out .= "\"" . $_escape ( ( string ) $key ) . "\": ";
		
		if (is_object ( $value ) || is_array ( $value )) {
			$out .= "\n";
			$out .= $_myself ( $value, $indent + 1 );
		} elseif (is_bool ( $value )) {
			$out .= $value ? 'true' : 'false';
		} elseif (is_null ( $value )) {
			$out .= 'null';
		} elseif (is_string ( $value )) {
			$out .= "\"" . $_escape ( $value ) . "\"";
		} else {
			$out .= $value;
		}
		
		$out .= ",\n";
	}
	
	if (! empty ( $out )) {
		$out = substr ( $out, 0, - 2 );
	}
	
	$out = str_repeat ( "\t", $indent ) . "{\n" . $out;
	$out .= "\n" . str_repeat ( "\t", $indent ) . "}";
	
	return $out;
}
function add_translation($key, $value) {
	register_translation ( $key, $value );
}
function register_translation($key, $value) {
	$key = strtoupper ( $key );
	if (! startswith ( $key, "TRANSLATION_" )) {
		$key = "TRANSLATION_" . $key;
	}
	idefine ( $key, $value );
}

// returns true if $needle is a substring of $haystack
function str_contains($needle, $haystack) {
	return strpos ( $haystack, $needle ) !== false;
}

// Get a subset of an associative array by providing the keys.
function array_keep($array, $keys) {
	return array_intersect_key ( $array, array_fill_keys ( $keys, null ) );
}
function getAllUsedLanguages() {
	$sql = "select language from `{prefix}content` where active = 1 group by language order by language";
	$query = Database::query ( $sql, true );
	$languages = array ();
	while ( $row = Database::fetchobject ( $query ) ) {
		$languages [] = $row->language;
	}
	return $languages;
}

// prepares a text / code for html output
// replaces new lines with <br> tags
function preparePlainTextforHTMLOutput($text) {
	return nl2br ( htmlspecialchars ( $text ) );
}
function get_action() {
	if (isset ( $_REQUEST ["action"] )) {
		return $_REQUEST ["action"];
	} else {
		return "home";
	}
}
function isMaintenanceMode() {
	return (strtolower ( Settings::get ( "maintenance_mode" ) ) == "on" || strtolower ( Settings::get ( "maintenance_mode" ) ) == "true" || Settings::get ( "maintenance_mode" ) == "1");
}
function getStringLengthInBytes($data) {
	return ini_get ( 'mbstring.func_overload' ) ? mb_strlen ( $data, '8bit' ) : strlen ( $data );
}

// sind wir gerade im Adminordner?
function is_admin_dir() {
	return basename ( getcwd () ) === "admin";
}
function initconfig($key, $value) {
	$retval = false;
	if (! Settings::get ( $key )) {
		setconfig ( $key, $value );
		$retval = true;
		SettingsCache::set ( $key, $value );
	}
	return $retval;
}
function set_format($format) {
	$_GET ["format"] = trim ( $format, "." );
}
function get_jquery_url() {
	$url = "admin/scripts/jquery.min.js";
	$url = apply_filter ( $url, "jquery_url" );
	return $url;
}
function get_prefered_language(array $available_languages, $http_accept_language) {
	$available_languages = array_flip ( $available_languages );
	
	$langs;
	preg_match_all ( '~([\w-]+)(?:[^,\d]+([\d.]+))?~', strtolower ( $http_accept_language ), $matches, PREG_SET_ORDER );
	foreach ( $matches as $match ) {
		
		list ( $a, $b ) = explode ( '-', $match [1] ) + array (
				'',
				'' 
		);
		$value = isset ( $match [2] ) ? ( float ) $match [2] : 1.0;
		
		if (isset ( $available_languages [$match [1]] )) {
			$langs [$match [1]] = $value;
			continue;
		}
		
		if (isset ( $available_languages [$a] )) {
			$langs [$a] = $value - 0.1;
		}
	}
	arsort ( $langs );
	
	return $langs;
}
function get_all_used_menus() {
	$retval = array ();
	$query = db_query ( "select menu from " . tbname ( "content" ) . " group by menu" );
	while ( $row = db_fetch_object ( $query ) ) {
		$retval [] = $row->menu;
	}
	return $retval;
}
function get_shortlink($id = null) {
	if (is_null ( $id )) {
		$shortlink = null;
		$id = get_ID ();
	}
	if ($id) {
		$shortlink = getBaseFolderURL () . "/?goid=" . get_ID ();
	}
	
	$shortlink = apply_filter ( $shortlink, "shortlink" );
	return $shortlink;
}
function get_canonical() {
	$canonical = getBaseFolderURL () . "/";
	if (! is_frontpage ()) {
		$canonical .= buildSEOUrl ();
	}
	
	if (containsModule ( null, "blog" )) {
		if (isset ( $_GET ["single"] )) {
			$canonical .= "?single=" . htmlspecialchars ( $_GET ["single"] );
		} else if (isset ( $_GET ["limit"] )) {
			$canonical .= "?limit=" . intval ( $_GET ["limit"] );
		}
	}
	$canonical = apply_filter ( $canonical, "canonical" );
	return $canonical;
}
function is_crawler($useragent = null) {
	if (is_null ( $useragent )) {
		$useragent = $_SERVER ['HTTP_USER_AGENT'];
	}
	$isCrawler = apply_filter ( $useragent, "is_crawler" );
	if (is_bool ( $isCrawler ) or is_int ( $isCrawler )) {
		return boolval ( $isCrawler );
	}
	
	$crawlers = 'Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|' . 'AcioRobot|ASPSeek|CocoCrawler|Dumbot|FAST-WebCrawler|' . 'GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot|eStyle|Scrubby';
	$isCrawler = (preg_match ( "/$crawlers/", $useragent ) > 0);
	return $isCrawler;
}
function get_lang_config($name, $lang) {
	$retval = false;
	$config = Settings::get ( $name . "_" . $lang );
	if ($config) {
		$retval = $config;
	} else {
		$config = Settings::get ( $name );
	}
	return $config;
}

// Check if it is night (current hour between 0 and 4 o'Clock AM)
function is_night() {
	$hour = ( int ) date ( "G", time () );
	return ($hour >= 0 and $hour <= 4);
}
function eTagFromString($str) {
	header ( 'ETag: ' . md5 ( $str ) );
}

// Browser soll nur einen Tag Cachen
// Für statische Ressourcen nutzen
function browsercacheOneDay($modified = null) {
	header ( 'Cache-Control: public' );
	header ( "Expires: " . gmdate ( "D, d M Y H:i:s", time () + 86400 ) . " GMT" );
	header ( "Cache-Control: public,max-age=86400" );
	if (! is_null ( $modified )) {
		header ( "Last-Modified: " . gmdate ( "D, d M Y H:i:s", $modified ) . " GMT" );
		if (isset ( $_SERVER ['HTTP_IF_MODIFIED_SINCE'] ) && $modified <= strtotime ( $_SERVER ['HTTP_IF_MODIFIED_SINCE'] )) {
			$_SERVER ["ulicms_send_304"];
			header ( "HTTP/1.1 304 Not Modified" );
			exit ();
		}
	}
}

// PHP Formbuilder Class initialisieren
function initPFBC() {
	do_event ( "init_pfbc" );
}
function is_debug_mode() {
	$config = new CMSConfig ();
	return (defined ( "ULICMS_DEBUG" ) and ULICMS_DEBUG) or (isset ( $config->debug ) and $config->debug);
}
function isCLI() {
	return php_sapi_name () == "cli";
}

/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email
 *        	The email address
 * @param string $s
 *        	Size in pixels, defaults to 80px [ 1 - 2048 ]
 * @param string $d
 *        	Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
 * @param string $r
 *        	Maximum rating (inclusive) [ g | pg | r | x ]
 * @param bool $img
 *        	True to return a complete IMG tag False for just the URL
 * @param array $atts
 *        	Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 *         @source http://gravatar.com/site/implement/images/php/
 */
function get_gravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array()) {
	// Nach dem in Kraft treten, der Datenschutz-Grundverordnung 2018
	// wird die Nutzung von Gravatar in Deutschland illegal
	// daher wird an dieser Stelle die Gravatar-Integration gekappt
	// und stattdessen wird ein statisches Platzhalter-Bild angezeigt
	// Bis ein integrierter Avatar-Upload in UliCMS implementiert ist.
	$url = ModuleHelper::getBaseUrl ( "/admin/gfx/no_avatar.png" );
	if ($img) {
		$url = '<img src="' . $url . '"';
		foreach ( $atts as $key => $val )
			$url .= ' ' . $key . '="' . $val . '"';
		$url .= ' />';
	}
	return $url;
}

// Random string generieren (für Passwort)
function rand_string($length) {
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	return substr ( str_shuffle ( $chars ), 0, $length );
}
function getLanguageFilePath($lang = "de") {
	return ULICMS_ROOT . "/lang/" . $lang . ".php";
}

// Gibt den für den derzeit eingeloggten User eingestellten HTML-Editor aus.
// Wenn der Anwender nicht eingeloggt ist return null;
function get_html_editor() {
	if (! is_logged_in ()) {
		return null;
	}
	$query = db_query ( "SELECT html_editor from " . tbname ( "users" ) . " where id = " . get_user_id () );
	if (! $query) {
		return "ckeditor";
	}
	
	$obj = db_fetch_assoc ( $query );
	if (! is_null ( $obj ["html_editor"] ) and ! empty ( $obj ["html_editor"] )) {
		return $obj ["html_editor"];
	} else {
		return "ckeditor";
	}
}

// Den aktuellen HTTP Request in der `log` Tabelle protokollieren
function log_request($save_ip = false) {
	do_event ( "before_log_request" );
	if ($save_ip) {
		$ip = get_ip ();
	} else {
		$ip = "";
	}
	
	$ip = db_escape ( $ip );
	$request_method = db_escape ( get_request_method () );
	$useragent = db_escape ( get_useragent () );
	$request_uri = db_escape ( get_request_uri () );
	$http_host = db_escape ( get_http_host () );
	$referrer = db_escape ( get_referrer () );
	
	db_query ( "INSERT INTO " . tbname ( "log" ) . " (ip, request_method, useragent, request_uri, http_host, referrer) VALUES('$ip', '$request_method', '$useragent', '$request_uri','$http_host', '$referrer')" );
	
	do_event ( "after_log_request" );
}

// Prüfen, ob Anti CSRF Token vorhanden ist
// Siehe http://de.wikipedia.org/wiki/Cross-Site-Request-Forgery
function check_csrf_token() {
	if (! isset ( $_REQUEST ["csrf_token"] )) {
		return false;
	}
	return $_REQUEST ["csrf_token"] == $_SESSION ["csrf_token"];
}
function check_form_timestamp() {
	if (Settings::get ( "spamfilter_enabled" ) != "yes") {
		return;
	}
	
	$original_timestamp = Request::getVar ( "form_timestamp", 0, "int" );
	$min_time_to_fill_form = Settings::get ( "min_time_to_fill_form", "int" );
	if (time () - $original_timestamp < $min_time_to_fill_form) {
		setconfig ( "contact_form_refused_spam_mails", getconfig ( "contact_form_refused_spam_mails" ) + 1 );
		HTMLResult ( "Spam detected based on timestamp.", 400 );
	}
}

// HTML Code für Anti CSRF Token zurückgeben
// Siehe http://de.wikipedia.org/wiki/Cross-Site-Request-Forgery
function get_csrf_token_html() {
	$html = '<input type="hidden" name="csrf_token" value="' . get_csrf_token () . '">';
	if (Settings::get ( "min_time_to_fill_form", "int" ) > 0) {
		$html .= '<input type="hidden" name="form_timestamp" value="' . time () . '">';
	}
	return $html;
}
function csrf_token_html() {
	echo get_csrf_token_html ();
}
function get_csrf_token() {
	if (! isset ( $_SESSION ["csrf_token"] )) {
		$_SESSION ["csrf_token"] = md5 ( uniqid ( rand (), true ) );
	}
	return $_SESSION ["csrf_token"];
}
function getFieldsForCustomType($type) {
	$fields = array ();
	$modules = getAllModules ();
	foreach ( $modules as $module ) {
		$custom_types = getModuleMeta ( $module, "custom_types" );
		if ($custom_types) {
			foreach ( $custom_types as $key => $value ) {
				if ($key == $type) {
					foreach ( $value as $field ) {
						$fields [] = $field;
					}
				}
			}
		}
	}
	return $fields;
}
function get_used_post_types() {
	$query = Database::query ( "select `type` from {prefix}content group by `type`", true );
	$types = get_available_post_types ();
	$used_types = array ();
	$return_types = array ();
	while ( $row = Database::fetchObject ( $query ) ) {
		$used_types [] = $row->type;
	}
	foreach ( $types as $type ) {
		if (faster_in_array ( $type, $used_types )) {
			$return_types [] = $type;
		}
	}
	return $return_types;
}
function get_available_post_types() {
	$types = DefaultContentTypes::getAll ();
	$types = array_keys ( $types );
	return $types;
}

// Schriftgrößen zurückgeben
function getFontSizes() {
	global $sizes;
	$sizes = array ();
	for($i = 6; $i <= 80; $i ++) {
		$sizes [] = $i . "px";
	}
	do_event ( "custom_font_sizes" );
	
	$sizes = apply_filter ( $sizes, "font_sizes" );
	return $sizes;
}
function getModuleMeta($module, $attrib = null) {
	$retval = null;
	$metadata_file = getModulePath ( $module, true ) . "metadata.json";
	if (is_file ( $metadata_file )) {
		if ($attrib != null) {
			$data = ! Vars::get ( "module_{$module}_meta" ) ? file_get_contents ( $metadata_file ) : Vars::get ( "module_{$module}_meta" );
			if (is_string ( $data )) {
				$data = json_decode ( $data, true );
			}
			Vars::set ( "module_{$module}_meta", $data );
			if (isset ( $data [$attrib] )) {
				$retval = $data [$attrib];
			}
		} else {
			$data = ! Vars::get ( "module_{$module}_meta" ) ? file_get_contents ( $metadata_file ) : Vars::get ( "module_{$module}_meta" );
			if (is_string ( $data )) {
				$data = json_decode ( $data, true );
			}
			Vars::set ( "module_{$module}_meta", $data );
			
			$retval = $data;
		}
	}
	return $retval;
}
function getThemeMeta($theme, $attrib = null) {
	$retval = null;
	$metadata_file = getTemplateDirPath ( $theme, true ) . "metadata.json";
	if (is_file ( $metadata_file )) {
		$data = ! Vars::get ( "theme_{$theme}_meta" ) ? file_get_contents ( $metadata_file ) : Vars::get ( "theme_{$theme}_meta" );
		
		if (is_string ( $data )) {
			$data = json_decode ( $data, true );
		}
		Vars::set ( "theme_{$theme}_meta", $data );
		if ($attrib != null) {
			if (isset ( $data[$attrib] )) {
				$retval = $data[$attrib];
			}
		} else {
			$retval = $data;
		}
	}
	return $retval;
}
function getModuleName($module) {
	$name_file = getModulePath ( $module ) . $module . "_name.php";
	if (! is_file ( $name_file )) {
		return $module;
	}
	include_once $name_file;
	$name_function = $module . "_name";
	if (function_exists ( $name_function )) {
		return call_user_func ( $name_function );
	} else {
		return $module;
	}
}
function getLanguageNameByCode($code) {
	$query = db_query ( "SELECT name FROM `" . tbname ( "languages" ) . "` WHERE language_code = '" . db_escape ( $code ) . "'" );
	$retval = $code;
	if (db_num_rows ( $query ) > 0) {
		$result = db_fetch_object ( $query );
		$retval = $result->name;
	}
	
	return $retval;
}
function getAvailableBackendLanguages() {
	$langdir = ULICMS_ROOT . "/lang/";
	$list = scandir ( $langdir );
	sort ( $list );
	$retval = array ();
	for($i = 0; $i < count ( $list ); $i ++) {
		if (endsWith ( $list [$i], ".php" )) {
			array_push ( $retval, basename ( $list [$i], ".php" ) );
		}
	}
	
	return $retval;
}
function getSystemLanguage() {
	if (isset ( $_SESSION ["system_language"] )) {
		$lang = $_SESSION ["system_language"];
	} else if (isset ( $_SESSION ["language"] )) {
		$lang = $_SESSION ["language"];
	} else if (Settings::get ( "system_language" )) {
		$lang = Settings::get ( "system_language" );
	} else {
		$lang = "de";
	}
	if (! is_file ( getLanguageFilePath ( $lang ) )) {
		$lang = "de";
	}
	return $lang;
}
function getDomainByLanguage($givenLanguage) {
	$domainMapping = Settings::get ( "domain_to_language" );
	$domainMapping = Settings::mappingStringToArray ( $domainMapping );
	foreach ( $domainMapping as $domain => $language ) {
		if ($givenLanguage == $language) {
			return $domain;
		}
	}
	
	return null;
}
function getLanguageByDomain($givenDomain) {
	$domainMapping = Settings::get ( "domain_to_language" );
	$domainMapping = Settings::mappingStringToArray ( $domainMapping );
	foreach ( $domainMapping as $domain => $language ) {
		if ($givenDomain == $domain) {
			return $language;
		}
	}
	
	return null;
}
function setLanguageByDomain() {
	$domainMapping = Settings::get ( "domain_to_language" );
	$domainMapping = Settings::mappingStringToArray ( $domainMapping );
	foreach ( $domainMapping as $domain => $language ) {
		$givenDomain = $_SERVER ["HTTP_HOST"];
		if ($domain == $givenDomain and faster_in_array ( $language, getAllLanguages () )) {
			$_SESSION ["language"] = $language;
			return true;
		}
	}
	return false;
}
function getOnlineUsers() {
	$users_online = db_query ( "SELECT username FROM " . tbname ( "users" ) . " WHERE last_action > " . (time () - 300) . " ORDER BY username" );
	$users = array ();
	while ( $row = db_fetch_object ( $users_online ) ) {
		array_push ( $users, $row->username );
	}
	return $users;
}
function rootDirectory() {
	$pageURL = 'http';
	if ($_SERVER ["HTTPS"] == "on") {
		$pageURL .= "s";
	}
	$pageURL .= "://";
	$dirname = dirname ( $_SERVER ["REQUEST_URI"] );
	$dirname = str_replace ( "\\", "/", $dirname );
	$dirname = trim ( $dirname, "/" );
	if ($dirname != "") {
		$dirname = "/" . $dirname . "/";
	} else {
		$dirname = "/";
	}
	if ($_SERVER ["SERVER_PORT"] != "80" && $_SERVER ["SERVER_PORT"] != "443") {
		$pageURL .= $_SERVER ["SERVER_NAME"] . ":" . $_SERVER ["SERVER_PORT"] . $dirname;
	} else {
		$pageURL .= $_SERVER ["SERVER_NAME"] . $dirname;
	}
	return $pageURL;
}

// Alternative PHP Cache leeren, sofern installiert und aktiv
function clearAPCCache() {
	if (! function_exists ( "apc_clear_cache" )) {
		return false;
	}
	apc_clear_cache ();
	apc_clear_cache ( 'user' );
	apc_clear_cache ( 'opcode' );
	return true;
}

// Alle Caches leeren
// Sowohl den Seiten-Cache, den Download/Paketmanager Cache
// als auch den APC Bytecode Cache
function clearCache() {
	CacheUtil::clearCache ();
}

// DEPRECATED:
// This function may be removed in future releases of UliCMS
// Use do_event()
function add_hook($name) {
	trigger_error ( "add_hook() is deprecated. Please use do_event().", E_USER_DEPRECATED );
	do_event ( $name );
}
function do_event($name) {
	// don't run this code on kcfinder page (media)
	// since the "Path" class has a naming conflict with the same named
	// class of KCFinder
	if (defined ( "KCFINDER_PAGE" )) {
		return;
	}
	$modules = getAllModules ();
	$disabledModules = Vars::get ( "disabledModules" );
	for($hook_i = 0; $hook_i < count ( $modules ); $hook_i ++) {
		if (faster_in_array ( $modules [$hook_i], $disabledModules )) {
			continue;
		}
		$file1 = getModulePath ( $modules [$hook_i], true ) . $modules [$hook_i] . "_" . $name . ".php";
		$file2 = getModulePath ( $modules [$hook_i], true ) . "hooks/" . $name . ".php";
		$main_class = getModuleMeta ( $modules [$hook_i], "main_class" );
		$controller = null;
		if ($main_class) {
			$controller = ControllerRegistry::get ( $main_class );
		}
		$escapedName = ModuleHelper::underscoreToCamel ( $name );
		if ($controller and method_exists ( $controller, $escapedName )) {
			echo $controller->$escapedName ();
		} else if (is_file ( $file1 )) {
			@include_once $file1;
		} else if (is_file ( $file2 )) {
			@include_once $file2;
		}
	}
}
function cms_release_year() {
	$v = new UliCMSVersion ();
	echo $v->getReleaseYear ();
}
function splitAndTrim($str) {
	return array_map ( 'trim', explode ( ";", $str ) );
}
function setLocaleByLanguage() {
	$locale = null;
	if (is_admin_dir ()) {
		$var = "locale_" . db_escape ( $_SESSION ["system_language"] );
	} else {
		$var = "locale_" . db_escape ( $_SESSION ["language"] );
	}
	$locale = Settings::get ( $var );
	if ($locale) {
		$locale = splitAndTrim ( $locale );
		array_unshift ( $locale, LC_ALL );
		@call_user_func_array ( "setlocale", $locale );
	} else {
		$locale = Settings::get ( "locale" );
		if ($locale) {
			
			$locale = splitAndTrim ( $locale );
			array_unshift ( $locale, LC_ALL );
			@call_user_func_array ( "setlocale", $locale );
		}
	}
	return $locale;
}

// Returns the language code of the current language
// If $current is true returns language of the current page
// else it returns $_SESSION["language"];
function getCurrentLanguage($current = false) {
	if (Vars::get ( "current_language_" . strbool ( $current ) )) {
		return Vars::get ( "current_language_" . strbool ( $current ) );
	}
	if ($current) {
		$query = db_query ( "SELECT language FROM " . tbname ( "content" ) . " WHERE systemname='" . get_requested_pagename () . "'" );
		if (db_num_rows ( $query ) > 0) {
			$fetch = db_fetch_object ( $query );
			$language = $fetch->language;
			Vars::set ( "current_language_" . strbool ( $current ), $language );
		}
	}
	
	if (isset ( $_SESSION ["language"] )) {
		return basename ( $_SESSION ["language"] );
	} else {
		return basename ( Settings::get ( "default_language" ) );
	}
}

// Auf automatische aktualisieren prüfen.
// Rückgabewert: ein String oder False
function checkForUpdates() {
	if (Settings::get ( "disable_core_update_check" )) {
		return false;
	}
	include_once ULICMS_ROOT . "/lib/file_get_contents_wrapper.php";
	$info = @file_get_contents_Wrapper ( UPDATE_CHECK_URL, true );
	if (! $info or trim ( $info ) === "") {
		return false;
	} else {
		return $info;
	}
}
function getThemeList() {
	return getThemesList ();
}
function getThemesList() {
	$pkg = new PackageManager ();
	return $pkg->getInstalledPackages ( 'themes' );
}
function getTemplateDirPath($sub = "default", $abspath = false) {
	if ($abspath) {
		$templateDir = Path::resolve ( "ULICMS_DATA_STORAGE_ROOT/content/templates/" ) . "/";
	} else if (ULICMS_ROOT != ULICMS_DATA_STORAGE_ROOT and defined ( "ULICMS_DATA_STORAGE_URL" )) {
		$templateDir = Path::resolve ( "ULICMS_DATA_STORAGE_URL/content/templates" ) . "/";
	} else if (is_admin_dir ()) {
		$templateDir = "../content/templates/";
	} else {
		$templateDir = "content/templates/";
	}
	
	$templateDir = $templateDir . $sub . "/";
	return $templateDir;
}
function getModuleAdminSelfPath() {
	$self_path = $_SERVER ["REQUEST_URI"];
	$self_path = str_replace ( '"', '', $self_path );
	$self_path = str_replace ( "'", '', $self_path );
	return $self_path;
}

// replace num entity with the character
function replace_num_entity($ord) {
	$ord = $ord [1];
	if (preg_match ( '/^x([0-9a-f]+)$/i', $ord, $match )) {
		$ord = hexdec ( $match [1] );
	} else {
		$ord = intval ( $ord );
	}
	
	$no_bytes = 0;
	$byte = array ();
	
	if ($ord < 128) {
		return chr ( $ord );
	} elseif ($ord < 2048) {
		$no_bytes = 2;
	} elseif ($ord < 65536) {
		$no_bytes = 3;
	} elseif ($ord < 1114112) {
		$no_bytes = 4;
	} else {
		return;
	}
	
	switch ($no_bytes) {
		case 2 :
			{
				$prefix = array (
						31,
						192 
				);
				break;
			}
		case 3 :
			{
				$prefix = array (
						15,
						224 
				);
				break;
			}
		case 4 :
			{
				$prefix = array (
						7,
						240 
				);
			}
	}
	
	for($i = 0; $i < $no_bytes; $i ++) {
		$byte [$no_bytes - $i - 1] = (($ord & (63 * pow ( 2, 6 * $i ))) / pow ( 2, 6 * $i )) & 63 | 128;
	}
	
	$byte [0] = ($byte [0] & $prefix [0]) | $prefix [1];
	
	$ret = '';
	for($i = 0; $i < $no_bytes; $i ++) {
		$ret .= chr ( $byte [$i] );
	}
	
	return $ret;
}

// This Returns the current full URL
// for example: http://www.homepage.de/news.html?single=title
function getBaseFolderURL() {
	$s = empty ( $_SERVER ["HTTPS"] ) ? '' : ($_SERVER ["HTTPS"] == "on") ? "s" : "";
	$sp = strtolower ( $_SERVER ["SERVER_PROTOCOL"] );
	$protocol = substr ( $sp, 0, strpos ( $sp, "/" ) ) . $s;
	$port = ($_SERVER ["SERVER_PORT"] == "80" || $_SERVER ["SERVER_PORT"] == "443") ? "" : (":" . $_SERVER ["SERVER_PORT"]);
	return trim ( rtrim ( $protocol . "://" . $_SERVER ['SERVER_NAME'] . $port . str_replace ( "\\", "/", dirname ( $_SERVER ['REQUEST_URI'] ) ) ), "/" );
}

// This Returns the current full URL
// for example: http://www.homepage.de/news.html?single=title
function getCurrentURL() {
	$s = empty ( $_SERVER ["HTTPS"] ) ? '' : ($_SERVER ["HTTPS"] == "on") ? "s" : "";
	$sp = strtolower ( $_SERVER ["SERVER_PROTOCOL"] );
	$protocol = substr ( $sp, 0, strpos ( $sp, "/" ) ) . $s;
	$port = ($_SERVER ["SERVER_PORT"] == "80" || $_SERVER ["SERVER_PORT"] == "443") ? "" : (":" . $_SERVER ["SERVER_PORT"]);
	return $protocol . "://" . $_SERVER ['SERVER_NAME'] . $port . $_SERVER ['REQUEST_URI'];
}
function SureRemoveDir($dir, $DeleteMe) {
	if (! $dh = @opendir ( $dir ))
		return;
	while ( false !== ($obj = readdir ( $dh )) ) {
		if ($obj == '.' || $obj == '..')
			continue;
		if (! @unlink ( $dir . '/' . $obj ))
			SureRemoveDir ( $dir . '/' . $obj, true );
	}
	
	closedir ( $dh );
	if ($DeleteMe) {
		@rmdir ( $dir );
	}
}

/**
 * Generate path to Page
 * Argumente
 * String $page (Systemname)
 * Rückgabewert String im Format
 * ../seite.html
 * bzw.
 * seite.html;
 */
function buildSEOUrl($page = false, $redirection = null, $format = "html") {
	if (! is_null ( $redirection ) and ! empty ( $redirection )) {
		return $redirection;
	}
	if ($page === false)
		$page = get_requested_pagename ();
	
	if (startsWith ( $redirection, "#" )) {
		return $redirection;
	}
	
	if ($page === get_frontpage ()) {
		return "./";
	}
	
	$seo_url = "";
	
	if (is_file ( "backend.php" )) {
		$seo_url .= "../";
	}
	$seo_url .= $page;
	$seo_url .= "." . trim ( $format, "." );
	return $seo_url;
}
function getModulePath($module, $abspath = false) {
	if ($abspath) {
		return Path::resolve ( "ULICMS_DATA_STORAGE_ROOT/content/modules/$module" ) . "/";
	}
	if (ULICMS_ROOT == ULICMS_DATA_STORAGE_ROOT and ! defined ( "ULICMS_DATA_STORAGE_URL" )) {
		// Frontend Directory
		if (is_file ( "CMSConfig.php" )) {
			$module_folder = "content/modules/";
		} // Backend Directory
else {
			$module_folder = "../content/modules/";
		}
	} else {
		$module_folder = Path::resolve ( "ULICMS_DATA_STORAGE_URL/content/modules" ) . "/";
	}
	
	$available_modules = array ();
	return $module_folder . $module . "/";
}
function getModuleAdminFilePath($module) {
	return getModulePath ( $module, true ) . $module . "_admin.php";
}
function getModuleAdminFilePath2($module) {
	return getModulePath ( $module, true ) . "admin.php";
}
function getModuleMainFilePath($module) {
	return getModulePath ( $module, true ) . $module . "_main.php";
}
function getModuleMainFilePath2($module) {
	return getModulePath ( $module, true ) . "main.php";
}
function getModuleUninstallScriptPath($module, $abspath = true) {
	return getModulePath ( $module, $abspath ) . $module . "_uninstall.php";
}
function getModuleUninstallScriptPath2($module, $abspath = true) {
	return getModulePath ( $module, $abspath ) . "uninstall.php";
}

/**
 * outputCSV creates a line of CSV and outputs it to browser
 */
function outputCSV($array) {
	$fp = fopen ( 'php://output', 'w' ); // this file actual writes to php output
	fputcsv ( $fp, $array );
	fclose ( $fp );
}

/**
 * getCSV creates a line of CSV and returns it.
 */
function getCSV($array) {
	ob_start (); // buffer the output ...
	outputCSV ( $array );
	return ob_get_clean (); // ... then return it as a string!
}

/**
 * Output buffer flusher
 * Forces a flush of the output buffer to screen useful for displaying long loading lists eg: bulk emailers on screen
 * Stops the end user seeing loads of just plain old white and thinking the browser has crashed on long loading pages.
 */
function fcflush() {
	static $output_handler = null;
	if ($output_handler === null) {
		$output_handler = @ini_get ( 'output_handler' );
	}
	if ($output_handler == 'ob_gzhandler') {
		// forcing a flush with this is very bad
		return;
	}
	flush ();
	if (function_exists ( 'ob_flush' ) and function_exists ( 'ob_get_length' ) and ob_get_length () !== false) {
		ob_flush ();
	} else if (function_exists ( 'ob_end_flush' ) and function_exists ( 'ob_start' ) and function_exists ( 'ob_get_length' ) and ob_get_length () !== FALSE) {
		@ob_end_flush ();
		@ob_start ();
	}
}
function isModuleInstalled($name) {
	return faster_in_array ( $name, getAllModules () );
}
function getAllModules() {
	if (Vars::get ( "allModules" )) {
		return Vars::get ( "allModules" );
	}
	$pkg = new PackageManager ();
	$modules = $pkg->getInstalledPackages ( 'modules' );
	Vars::set ( "allModules", $modules );
	return $modules;
}
function no_cache($do = false) {
	if ($do) {
		Flags::setNoCache ( true );
	} else if (get_cache_control () == "auto" || get_cache_control () == "no_cache") {
		Flags::setNoCache ( true );
	}
}
function no_anti_csrf() {
	if (! defined ( "NO_ANTI_CSRF" )) {
		define ( "NO_ANTI_CSRF", true );
	}
}

// replace Shortcodes with modules
function replaceShortcodesWithModules($string, $replaceOther = true) {
	if ($replaceOther) {
		$string = str_ireplace ( '[title]', get_title (), $string );
		ob_start ();
		logo ();
		$string = str_ireplace ( '[logo]', ob_get_clean (), $string );
		$language = getCurrentLanguage ( true );
		$checkbox = new PrivacyCheckbox ( $language );
		$string = str_ireplace ( "[accept_privacy_policy]", $checkbox->render (), $string );
		ob_start ();
		motto ();
		$string = str_ireplace ( '[motto]', ob_get_clean (), $string );
		ob_start ();
		motto ();
		$string = str_ireplace ( '[slogan]', ob_get_clean (), $string );
		$current_page = get_page ();
		$string = str_ireplace ( '[category]', get_category (), $string );
		$token = get_csrf_token_html ();
		
		$token .= '<input type="url" name="my_homepage_url" class="antispam_honeypot" value="" autocomplete="off">';
		$string = str_ireplace ( '[csrf_token_html]', $token, $string );
		// [tel] Links for tel Tags
		$string = preg_replace ( '/\[tel\]([^\[\]]+)\[\/tel\]/i', '<a href="tel:$1" class="tel">$1</a>', $string );
		$string = preg_replace ( '/\[skype\]([^\[\]]+)\[\/skype\]/i', '<a href="skye:$1?call" class="skype">$1</a>', $string );
		
		preg_match_all ( "/\[include=([0-9]+)]/i", $string, $match );
		
		if (count ( $match ) > 0) {
			// @FIXME: Potenzial zur Endlosschleife (Seite die sich selbst einbindet)
			for($i = 0; $i < count ( $match [0] ); $i ++) {
				$placeholder = $match [0] [$i];
				$id = unhtmlspecialchars ( $match [1] [$i] );
				$id = intval ( $id );
				$page = ContentFactory::getByID ( $id );
				if ($page) {
					$content = "";
					if ($page->active and checkAccess ( $page->access )) {
						$content = $page->content;
					}
					$string = str_ireplace ( $placeholder, $content, $string );
				}
			}
		}
	}
	$allModules = ModuleHelper::getAllEmbedModules ();
	$disabledModules = Vars::get ( "disabledModules" );
	for($i = 0; $i <= count ( $allModules ); $i ++) {
		$thisModule = $allModules [$i];
		if (faster_in_array ( $thisModule, $disabledModules )) {
			continue;
		}
		$stringToReplace1 = '[module="' . $thisModule . '"]';
		$stringToReplace2 = '[module=&quot;' . $thisModule . '&quot;]';
		
		$module_mainfile_path = getModuleMainFilePath ( $thisModule );
		$module_mainfile_path2 = getModuleMainFilePath2 ( $thisModule );
		
		if (is_file ( $module_mainfile_path ) and (str_contains ( $stringToReplace1, $string ) or str_contains ( $stringToReplace2, $string ))) {
			require_once $module_mainfile_path;
		} else if (is_file ( $module_mainfile_path2 )) {
			require_once $module_mainfile_path2;
		} else {
			$html_output = "<p class='ulicms_error'>Das Modul " . $thisModule . " konnte nicht geladen werden.</p>";
		}
		
		$main_class = getModuleMeta ( $thisModule, "main_class" );
		$controller = null;
		$hasRenderMethod = false;
		if ($main_class) {
			$controller = ControllerRegistry::get ( $main_class );
		}
		
		if ($controller and method_exists ( $controller, "render" )) {
			$html_output = $controller->render ();
		} else if (function_exists ( $thisModule . "_render" )) {
			$html_output = call_user_func ( $thisModule . "_render" );
		} else {
			$html_output = "<p class='ulicms_error'>Das Modul " . $thisModule . " konnte nicht geladen werden.</p>";
		}
		
		$string = str_replace ( $stringToReplace1, $html_output, $string );
		$string = str_replace ( $stringToReplace2, $html_output, $string );
		$string = str_replace ( '[title]', get_title (), $string );
	}
	$string = replaceVideoTags ( $string );
	$string = replaceAudioTags ( $string );
	return $string;
}
function getPageByID($id) {
	$id = intval ( $id );
	$query = db_query ( "SELECT * FROM " . tbname ( "content" ) . " where id = " . $id );
	if (db_num_rows ( $query ) > 0) {
		return db_fetch_object ( $query );
	} else {
		return null;
	}
}

// get page id by systemname
function getPageIDBySystemname($systemname) {
	$query = db_query ( "SELECT systemname, id FROM `" . tbname ( "content" ) . "` where systemname='" . db_escape ( $systemname ) . "'" );
	if (db_num_rows ( $query ) > 0) {
		$row = db_fetch_object ( $query );
		return $row->id;
	} else {
		return null;
	}
}
function getPageSystemnameByID($id) {
	$query = db_query ( "SELECT systemname, id FROM `" . tbname ( "content" ) . "` where id=" . intval ( $id ) );
	if (db_num_rows ( $query ) > 0) {
		$row = db_fetch_object ( $query );
		return $row->systemname;
	} else {
		return "-";
	}
}
function getPageTitleByID($id) {
	$query = db_query ( "SELECT title, id FROM `" . tbname ( "content" ) . "` where id=" . intval ( $id ) );
	if (db_num_rows ( $query ) > 0) {
		$row = db_fetch_object ( $query );
		return $row->title;
	} else {
		return "[" . get_translation ( "none" ) . "]";
	}
}

// Get systemnames of all pages
function getAllPagesWithTitle() {
	$query = db_query ( "SELECT systemname, id, title FROM `" . tbname ( "content" ) . "` WHERE `deleted_at` IS NULL ORDER BY systemname" );
	$returnvalues = Array ();
	while ( $row = db_fetch_object ( $query ) ) {
		$a = Array (
				$row->title,
				$row->systemname . ".html" 
		);
		array_push ( $returnvalues, $a );
		if (containsModule ( $row->systemname, "blog" )) {
			
			$sql = "select title, seo_shortname from " . tbname ( "blog" ) . " ORDER by datum DESC";
			$query_blog = db_query ( $sql );
			while ( $row_blog = db_fetch_object ( $query_blog ) ) {
				$title = $row->title . " -> " . $row_blog->title;
				$url = $row->systemname . ".html" . "?single=" . $row_blog->seo_shortname;
				$b = Array (
						$title,
						$url 
				);
				array_push ( $returnvalues, $b );
			}
		}
	}
	return $returnvalues;
}

// Get all pages
function getAllPages($lang = null, $order = "systemname", $exclude_hash_links = true, $menu = null) {
	if (! $lang) {
		if (! $menu) {
			$query = db_query ( "SELECT * FROM `" . tbname ( "content" ) . "` WHERE `deleted_at` IS NULL ORDER BY $order" );
		} else {
			$query = db_query ( "SELECT * FROM `" . tbname ( "content" ) . "` WHERE `deleted_at` IS NULL and menu = '" . Database::escapeValue ( $menu ) . "' ORDER BY $order" );
		}
	} else {
		if (! $menu) {
			$query = db_query ( "SELECT * FROM `" . tbname ( "content" ) . "` WHERE `deleted_at` IS NULL AND language ='" . db_escape ( $lang ) . "' ORDER BY $order" );
		} else {
			$query = db_query ( "SELECT * FROM `" . tbname ( "content" ) . "` WHERE `deleted_at` IS NULL AND language ='" . db_escape ( $lang ) . "' and menu = '" . Database::escapeValue ( $menu ) . "' ORDER BY $order" );
		}
	}
	$returnvalues = Array ();
	while ( $row = db_fetch_assoc ( $query ) ) {
		if (! $exclude_hash_links or ($exclude_hash_links and $row ["type"] != "link" and $row ["type"] != "node" and $row ["type"] != "language_link")) {
			array_push ( $returnvalues, $row );
		}
	}
	
	return $returnvalues;
}

// Get systemnames of all pages
function getAllSystemNames($lang = null) {
	if (! $lang) {
		$query = db_query ( "SELECT systemname,id FROM `" . tbname ( "content" ) . "` WHERE `deleted_at` IS NULL AND redirection NOT LIKE '#%' ORDER BY systemname" );
	} else {
		
		$query = db_query ( "SELECT systemname,id FROM `" . tbname ( "content" ) . "` WHERE `deleted_at` IS NULL  AND redirection NOT LIKE '#%' AND language ='" . db_escape ( $lang ) . "' ORDER BY systemname" );
	}
	$returnvalues = Array ();
	while ( $row = db_fetch_object ( $query ) ) {
		array_push ( $returnvalues, $row->systemname );
	}
	
	return $returnvalues;
}

// Sprachcodes abfragen und als Array zurück geben
function getAllLanguages($filtered = false) {
	if ($filtered) {
		$group = new Group ();
		$group->getCurrentGroup ();
		$languages = $group->getLanguages ();
		if (count ( $languages ) > 0) {
			$result = array ();
			foreach ( $languages as $lang ) {
				$result [] = $lang->getLanguageCode ();
			}
			return $result;
		}
	}
	if (! is_null ( Vars::get ( "all_languages" ) )) {
		return Vars::get ( "all_languages" );
	}
	$query = db_query ( "SELECT language_code FROM `" . tbname ( "languages" ) . "` ORDER BY language_code" );
	$returnvalues = Array ();
	while ( $row = db_fetch_object ( $query ) ) {
		array_push ( $returnvalues, $row->language_code );
	}
	Vars::set ( "all_languages", $returnvalues );
	return $returnvalues;
}

// get URL to UliCMS
function the_url() {
	$pageURL = 'http';
	if ($_SERVER ["HTTPS"] == "on") {
		$pageURL .= "s";
	}
	$pageURL .= "://";
	$dirname = dirname ( $_SERVER ["REQUEST_URI"] );
	$dirname = str_replace ( "\\", "/", $dirname );
	$dirname = str_replace ( "admin", "", $dirname );
	$dirname = trim ( $dirname, "/" );
	if ($dirname != "") {
		$dirname = "/" . $dirname . "/";
	} else {
		$dirname = "/";
	}
	if ($_SERVER ["SERVER_PORT"] != "80" && $_SERVER ["SERVER_PORT"] != "443") {
		$pageURL .= $_SERVER ["SERVER_NAME"] . ":" . $_SERVER ["SERVER_PORT"] . $dirname;
	} else {
		$pageURL .= $_SERVER ["SERVER_NAME"] . $dirname;
	}
	return $pageURL;
}

// Gibt die Identifier aller Menüs zurück.
// Zusätzliche Navigationsmenüs können definiert werden,
// durch setzen von additional_menus
function getAllMenus($only_used = false) {
	$menus = Array (
			"left",
			"top",
			"right",
			"bottom",
			"not_in_menu" 
	);
	$additional_menus = Settings::get ( "additional_menus" );
	
	if ($additional_menus) {
		$additional_menus = explode ( ";", $additional_menus );
		foreach ( $additional_menus as $m ) {
			array_push ( $menus, $m );
		}
	}
	if ($only_used) {
		$used = get_all_used_menus ();
		$new_menus = array ();
		for($i = 0; $i <= count ( $menus ); $i ++) {
			if (faster_in_array ( $menus [$i], $used )) {
				$new_menus [] = $menus [$i];
			}
		}
		$menus = $new_menus;
	}
	
	$themesList = getThemesList ();
	$allThemeMenus = array ();
	foreach ( $themesList as $theme ) {
		$themeMenus = getThemeMeta ( $theme, "menus" );
		if ($themeMenus and is_array ( $themeMenus )) {
			foreach ( $themeMenus as $m ) {
				if (! faster_in_array ( $m, $allThemeMenus )) {
					$allThemeMenus [] = $m;
				}
			}
		}
	}
	
	if (count ( $allThemeMenus ) > 0) {
		$menus = $allThemeMenus;
	}
	
	if (! faster_in_array ( "not_in_menu", $menus )) {
		$menus [] = "not_in_menu";
	}
	
	sort ( $menus );
	return $menus;
}

// Check if site contains a module
function containsModule($page = null, $module = false) {
	if (is_null ( $page )) {
		$page = get_requested_pagename ();
	}
	if (! is_null ( Vars::get ( "page_" . $page . "_contains_" . $module ) )) {
		return Vars::get ( "page_" . $page . "_contains_" . $module );
	}
	$query = db_query ( "SELECT content, module, `type` FROM " . tbname ( "content" ) . " WHERE systemname = '" . db_escape ( $page ) . "'" );
	$dataset = db_fetch_assoc ( $query );
	$content = $dataset ["content"];
	$content = str_replace ( "&quot;", "\"", $content );
	if (! is_null ( $dataset ["module"] ) and ! empty ( $dataset ["module"] ) and $dataset ["type"] == "module") {
		if (! $module or ($module and $dataset ["module"] == $module)) {
			Vars::set ( "page_" . $page . "_contains_" . $module, true );
			return true;
		}
	} else if ($module) {
		$result = preg_match ( "/\[module=\"" . preg_quote ( $module ) . "\"\]/", $content );
		Vars::set ( "page_" . $page . "_contains_" . $module, $result );
		return $result;
	} else {
		$result = preg_match ( "/\[module=\".+\"\]/", $content );
		Vars::set ( "page_" . $page . "_contains_" . $module, $result );
		return $result;
	}
	Vars::set ( "page_" . $page . "_contains_" . $module, false );
	return false;
}

// API-Aufruf zur Deinstallation eines Moduls
// Ruft uninstall Script auf, falls vorhanden
// Löscht anschließend den Ordner modules/$name
// TODO: dies in die PackageManager Klasse verschieben
function uninstall_module($name, $type = "module") {
	$acl = new ACL ();
	// Nur Admins können Module löschen
	if (! $acl->hasPermission ( "install_packages" ) and ! isCLI ()) {
		return false;
	}
	
	$name = trim ( $name );
	$name = basename ( $name );
	$name = trim ( $name );
	
	// Verhindern, dass der Modulordner oder gar das ganze
	// CMS gelöscht werden kann
	if ($name == "." or $name == ".." or empty ( $name )) {
		return false;
	}
	if ($type === "module") {
		$moduleDir = getModulePath ( $name, true );
		// Modul-Ordner entfernen
		if (is_dir ( $moduleDir )) {
			$uninstall_script = getModuleUninstallScriptPath ( $name, true );
			$uninstall_script2 = getModuleUninstallScriptPath2 ( $name, true );
			// Uninstall Script ausführen, sofern vorhanden
			$mainController = ModuleHelper::getMainController ( $name );
			if ($mainController and method_exists ( $mainController, "uninstall" )) {
				$mainController->uninstall ();
			} else if (is_file ( $uninstall_script )) {
				include $uninstall_script;
			} else if (is_file ( $uninstall_script2 )) {
				include $uninstall_script2;
			}
			sureRemoveDir ( $moduleDir, true );
			clearCache ();
			return ! is_dir ( $moduleDir );
		}
	} else if ($type === "theme") {
		$cTheme = Settings::get ( "theme" );
		$allThemes = getThemeList ();
		if (faster_in_array ( $name, $allThemes ) and $cTheme !== $name) {
			$theme_path = getTemplateDirPath ( $name, true );
			sureRemoveDir ( $theme_path, true );
			clearCache ();
			return ! is_dir ( $theme_path );
		}
	}
	
	return false;
}

// returns version number of UliCMS Core
function cms_version() {
	$v = new UliCMSVersion ();
	return implode ( ".", $v->getInternalVersion () );
}
function is_tablet() {
	if (! class_exists ( "Mobile_Detect" )) {
		return false;
	}
	$detect = new Mobile_Detect ();
	$result = $detect->isTablet ();
	return $result;
}

// 21. Februar 2015
// Nutzt nun die Klasse Mobile_Detect
function is_mobile() {
	if (! class_exists ( "Mobile_Detect" )) {
		return false;
	}
	$detect = new Mobile_Detect ();
	$result = $detect->isMobile ();
	if (Settings::get ( "no_mobile_design_on_tablet" ) and $result and $detect->isTablet ()) {
		$result = false;
	}
	if (function_exists ( "apply_filter" )) {
		$result = apply_filter ( $result, "is_mobile" );
	}
	return $result;
}
function func_enabled($func) {
	$disabled = explode ( ',', ini_get ( 'disable_functions' ) );
	foreach ( $disabled as $disableFunction ) {
		$is_disabled [] = trim ( $disableFunction );
	}
	if (faster_in_array ( $func, $is_disabled )) {
		$it_is_disabled ["m"] = $func . '() has been disabled for security reasons in php.ini';
		$it_is_disabled ["s"] = 0;
	} else {
		$it_is_disabled ["m"] = $func . '() is allow to use';
		$it_is_disabled ["s"] = 1;
	}
	return $it_is_disabled;
}
function is_admin() {
	if (! is_null ( Vars::get ( "is_admin" ) )) {
		return Vars::get ( "is_admin" );
	}
	$retval = false;
	$user_id = get_user_id ();
	if (! $user_id) {
		$retval = false;
	} else {
		$query = db_query ( "SELECT `admin` FROM " . tbname ( "users" ) . " where id = " . $user_id . " and admin = 1" );
		$retval = db_num_rows ( $query );
		
		return Vars::set ( "is_admin", db_num_rows ( $query ) );
	}
	return $retval;
}

// Mimetypen einer Datei ermitteln
function get_mime($file) {
	if (function_exists ( "finfo_file" )) {
		$finfo = finfo_open ( FILEINFO_MIME_TYPE ); // return mime type ala mimetype extension
		$mime = finfo_file ( $finfo, $file );
		finfo_close ( $finfo );
		return $mime;
	} else if (function_exists ( "mime_content_type" )) {
		return mime_content_type ( $file );
	} else if (! stristr ( ini_get ( "disable_functions" ), "shell_exec" )) {
		// http://stackoverflow.com/a/134930/1593459
		$file = escapeshellarg ( $file );
		$mime = shell_exec ( "file -bi " . $file );
		return $mime;
	} else {
		return false;
	}
}
function set_eTagHeaders($identifier, $timestamp) {
	$gmt_mTime = gmdate ( 'r', $timestamp );
	
	header ( 'Cache-Control: public' );
	header ( 'ETag: "' . md5 ( $timestamp . $identifier ) . '"' );
	header ( 'Last-Modified: ' . $gmt_mTime );
	
	if (isset ( $_SERVER ['HTTP_IF_MODIFIED_SINCE'] ) || isset ( $_SERVER ['HTTP_IF_NONE_MATCH'] )) {
		if ($_SERVER ['HTTP_IF_MODIFIED_SINCE'] == $gmt_mtime || str_replace ( '"', '', stripslashes ( $_SERVER ['HTTP_IF_NONE_MATCH'] ) ) == md5 ( $timestamp . $identifier )) {
			header ( 'HTTP/1.1 304 Not Modified' );
			exit ();
		}
	}
}
