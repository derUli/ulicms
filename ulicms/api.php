<?php

function get_format(){
   $format = "html";
   if(isset($_GET["format"])){
      $format = $_GET["format"];
   }
   return $format;
}

function set_format($format){
   $_GET["format"] = trim($format, ".");
}

function get_jquery_url(){
    $url = "//code.jquery.com/jquery-1.11.3.min.js";
    $url = apply_filter ( $url, "jquery_url" );
    return $url;
}

function get_prefered_language(array $available_languages, $http_accept_language) {

    $available_languages = array_flip($available_languages);

    $langs;
    preg_match_all('~([\w-]+)(?:[^,\d]+([\d.]+))?~', strtolower($http_accept_language), $matches, PREG_SET_ORDER);
    foreach($matches as $match) {

        list($a, $b) = explode('-', $match[1]) + array('', '');
        $value = isset($match[2]) ? (float) $match[2] : 1.0;

        if(isset($available_languages[$match[1]])) {
            $langs[$match[1]] = $value;
            continue;
        }

        if(isset($available_languages[$a])) {
            $langs[$a] = $value - 0.1;
        }

    }
    arsort($langs);

    return $langs;
}

function get_google_fonts(){
    $retval = array();
    $file = ULICMS_ROOT."/lib/webFontNames.opml";
    $content = file_get_contents($file);
    $xml =  new SimpleXMLElement($content);
    foreach($xml->body->outline as $outline){
            $retval[] = $outline["text"];
   };
   return $retval;
}

function get_all_used_menus(){
    $retval = array();
    $query = db_query("select menu from ".tbname("content")." group by menu");
	while($row = db_fetch_object($query)){
	   $retval[] = $row->menu;
	}
	return $retval;
}

function is_ajax_request() {
	return (! empty ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) && strtolower ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest');
}

function get_shortlink($id = null){

if(is_null($id)){
   $shortlink = null;
   $id = get_ID();
   }
   if($id){
       $shortlink = getBaseFolderURL(). "/?goid=".get_ID();
   }

   $shortlink = apply_filter ( $shortlink, "shortlink" );
   return $shortlink;
}

function get_canonical(){
   $canonical = getBaseFolderURL(). "/";
   if(!is_frontpage()){
               $canonical .= buildSEOUrl();
            }


	    if (containsModule ( null, "blog" )) {
                if(isset($_GET["single"])){
                   $canonical .= "?single=".htmlspecialchars($_GET["single"]);
                } else if(isset($_GET["limit"])){
                   $canonical .= "?limit=".intval($_GET["limit"]);
}
            }
   	$canonical = apply_filter ( $canonical, "canonical" );
        return $canonical;
}



function is_crawler($userAgent = null) {
	if (is_null ( $useragent )) {
		$useragent = $_SERVER ['HTTP_USER_AGENT'];
	}
	$crawlers = 'Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|' . 'AcioRobot|ASPSeek|CocoCrawler|Dumbot|FAST-WebCrawler|' . 'GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot|eStyle|Scrubby';
	$isCrawler = (preg_match ( "/$crawlers/", $userAgent ) > 0);
	return $isCrawler;
}
function get_lang_config($name, $lang) {
	$retval = false;
	$config = getconfig ( $name . "_" . $lang );
	if ($config)
		$retval = $config;
	else
		$config = getconfig ( $name );
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
	include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "PFBC" . DIRECTORY_SEPERATOR . "Form.php";
}
function is_debug_mode() {
	$config = new config ();
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
 * @param boole $img
 *        	True to return a complete IMG tag False for just the URL
 * @param array $atts
 *        	Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 *         @source http://gravatar.com/site/implement/images/php/
 */
function get_gravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array()) {
	$url = 'http://www.gravatar.com/avatar/';
	$url .= md5 ( strtolower ( trim ( $email ) ) );
	$url .= "?s=$s&d=$d&r=$r";
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
function getLanguageFilePath($lang = "de", $component = null) {
	// Todo Module Language Files
	return ULICMS_ROOT . "/lang/" . $lang . ".php";
}
function get_useragent() {
	return $_SERVER ['HTTP_USER_AGENT'];
}
function get_request_method() {
	return $_SERVER ["REQUEST_METHOD"];
}

// Gibt den für den derzeit eingeloggten User eingestellten HTML-Editor aus.
// Wenn der Anwender nicht eingeloggt ist return null;
function get_html_editor() {
	if (! is_logged_in ()) {
		return null;
	}
	$query = db_query ( "SELECT html_editor from " . tbname ( "users" ) . " where id = " . get_user_id () );
	if (! $query)
		return "ckeditor";

	$obj = db_fetch_assoc ( $query );
	if (! is_null ( $obj ["html_editor"] ) and ! empty ( $obj ["html_editor"] ))
		return $obj ["html_editor"];
	else
		return "ckeditor";
}
function get_request_uri() {
	return $_SERVER ["REQUEST_URI"];
}
function get_http_host() {
	return $_SERVER ["HTTP_HOST"];
}
function get_referer() {
	return get_referrer ();
}
function get_referrer() {
	$referrer = null;
	if (isset ( $_SERVER ['HTTP_REFERER'] )) {
		$referrer = $_SERVER ['HTTP_REFERER'];
	}
	return $referrer;
}

// Den aktuellen HTTP Request in der `log` Tabelle protokollieren
function log_request($save_ip = false) {
	add_hook ( "before_log_request" );
	if ($save_ip)
		$ip = get_ip ();
	else
		$ip = "";

	$ip = db_escape ( $ip );
	$request_method = db_escape ( get_request_method () );
	$useragent = db_escape ( get_useragent () );
	$request_uri = db_escape ( get_request_uri () );
	$http_host = db_escape ( get_http_host () );
	$referrer = db_escape ( get_referrer () );

	db_query ( "INSERT INTO " . tbname ( "log" ) . " (ip, request_method, useragent, request_uri, http_host, referrer) VALUES('$ip', '$request_method', '$useragent', '$request_uri','$http_host', '$referrer')" );

	add_hook ( "after_log_request" );
}

// Prüfen, ob Anti CSRF Token vorhanden ist
// Siehe http://de.wikipedia.org/wiki/Cross-Site-Request-Forgery
function check_csrf_token() {
	if (! isset ( $_REQUEST ["csrf_token"] ))
		return false;
	return $_REQUEST ["csrf_token"] == $_SESSION ["csrf_token"];
}

// HTML Code für Anti CSRF Token zurückgeben
// Siehe http://de.wikipedia.org/wiki/Cross-Site-Request-Forgery
function get_csrf_token_html() {
	return '<input type="hidden" name="csrf_token" value="' . get_csrf_token () . '">';
}
function csrf_token_html() {
	echo get_csrf_token_html ();
}
function get_csrf_token() {
	if (! isset ( $_SESSION ["csrf_token"] ))
		$_SESSION ["csrf_token"] = md5 ( uniqid ( rand (), true ) );
	return $_SESSION ["csrf_token"];
}

// returns site protocl
// http:// or https://
function get_site_protocol() {
	if (isset ( $_SERVER ['HTTPS'] ) && ($_SERVER ['HTTPS'] == 'on' || $_SERVER ['HTTPS'] == 1) || isset ( $_SERVER ['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER ['HTTP_X_FORWARDED_PROTO'] == 'https')
		return $protocol = 'https://';
	else
		return $protocol = 'http://';
}
function site_protocol() {
	echo get_site_protocol ();
}

// Ordner rekursiv kopieren
function recurse_copy($src, $dst) {
	$dir = opendir ( $src );
	@mkdir ( $dst );
	while ( false !== ($file = readdir ( $dir )) ) {
		if (($file != '.') && ($file != '..')) {
			if (is_dir ( $src . '/' . $file )) {
				recurse_copy ( $src . '/' . $file, $dst . '/' . $file );
			} else {
				copy ( $src . '/' . $file, $dst . '/' . $file );
			}
		}
	}
	closedir ( $dir );
}

// Aus einer Boolean einen String machen ("true" oder "false")
function strbool($value) {
	return $value ? 'true' : 'false';
}

// @TODO : Post Types implementieren.
function get_available_post_types() {
	global $post_types;
	$post_types = array (
			"page"
	);
	add_hook ( $post_types );

	sort ( $post_types );

	return $post_types;
}

// Schriftgrößen zurückgeben
// @TODO : Filter implementieren
// Schriftgrößen zurückgeben
// @TODO : Filter implementieren
function getFontSizes() {
	global $sizes;
	$sizes = array (
			"xx-small",
			"x-small",
			"smaller",
			"small",
			"medium",
			"large",
			"larger",
			"x-large",
			"xx-large"
	);
	add_hook ( "custom_font_sizes" );
	return $sizes;
}

// Die IP-Adresse des Clients zurückgeben
// Falls ein Proxy genutzt wurde, versuchen, die echte IP statt der
// des Proxy zu ermitteln
function get_ip() {
	$proxy_headers = array (
			'CLIENT_IP',
			'FORWARDED',
			'FORWARDED_FOR',
			'FORWARDED_FOR_IP',
			'HTTP_CLIENT_IP',
			'HTTP_FORWARDED',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED_FOR_IP',
			'HTTP_PC_REMOTE_ADDR',
			'HTTP_PROXY_CONNECTION',
			'HTTP_VIA',
			'HTTP_X_FORWARDED',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED_FOR_IP',
			'HTTP_X_IMFORWARDS',
			'HTTP_XROXY_CONNECTION',
			'VIA',
			'X_FORWARDED',
			'X_FORWARDED_FOR'
	);
	$regEx = "/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/";
	foreach ( $proxy_headers as $proxy_header ) {
		if (isset ( $_SERVER [$proxy_header] )) {
			/**
			 * HEADER ist gesetzt und dies ist eine gültige IP
			 */
			return $_SERVER [$proxy_header];
		} else if (stristr ( ',', $_SERVER [$proxy_header] ) !== false) {
			// Behandle mehrere IPs in einer Anfrage
			// (z.B.: X-Forwarded-For: client1, proxy1, proxy2)
			$proxy_header_temp = trim ( array_shift ( explode ( ',', $_SERVER [$proxy_header] ) ) );
			/**
			 * Teile in einzelne IPs, gib die letzte zurück und entferne Leerzeichen
			 */

			// if IPv4 address remove port if exists
			if (preg_match ( $regEx, $proxy_header_temp ) && ($pos_temp = stripos ( $proxy_header_temp, ':' )) !== false) {
				$proxy_header_temp = substr ( $proxy_header_temp, 0, $pos_temp );
			}
			return $proxy_header_temp;
		}
	}

	return $_SERVER ['REMOTE_ADDR'];
}
function getModuleMeta($module, $attrib = null) {
	$retval = null;
	$metadata_file = getModulePath ( $module ) . "metadata.json";
	if (file_exists ( $metadata_file )) {
		$data = file_get_contents ( $metadata_file );
		$data = json_decode ( $data );
		if ($attrib != null) {
			if (isset ( $data->$attrib )) {
				$retval = $data->$attrib;
			}
		} else {
			$retval = $data;
		}
	}
	return $retval;
}
function getThemeMeta($theme, $attrib = null) {
	$retval = null;
	$metadata_file = getTemplateDirPath ( $theme ) . "metadata.json";
	if (file_exists ( $metadata_file )) {
		$data = file_get_contents ( $metadata_file );
		$data = json_decode ( $data );
		if ($attrib != null) {
			if (isset ( $data->$attrib )) {
				$retval = $data->$attrib;
			}
		} else {
			$retval = $data;
		}
	}
	return $retval;
}
function getModuleName($module) {
	$name_file = getModulePath ( $module ) . $module . "_name.php";
	if (! file_exists ( $name_file )) {
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
	}
	else if(isset($_SESSION["language"])){
	    $lang = $_SESSION["language"];
	}
	else if (getconfig ( "system_language" )) {
		$lang = getconfig ( "system_language" );
	} else {
		$lang = "de";
	}

	if (! file_exists ( getLanguageFilePath ( $lang ) )) {
		$lang = "de";
	}

	return $lang;
}

// Übersetzung HTTP Status Code => Name
function getStatusCodeByNumber($nr) {
	$http_codes = array (
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => 'Switch Proxy',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			418 => 'I\'m a teapot',
			422 => 'Unprocessable Entity',
			423 => 'Locked',
			424 => 'Failed Dependency',
			425 => 'Unordered Collection',
			426 => 'Upgrade Required',
			449 => 'Retry With',
			450 => 'Blocked by Windows Parental Controls',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			506 => 'Variant Also Negotiates',
			507 => 'Insufficient Storage',
			509 => 'Bandwidth Limit Exceeded',
			510 => 'Not Extended'
	);

	return $nr . " " . $http_codes [$nr];
}

// Weiterleitung per Location header;
function ulicms_redirect($url = "http://www.ulicms.de", $status = 302) {
	header ( "HTTP/1.0 " . getStatusCodeByNumber ( $status ) );
	header ( "Location: " . $url );
	exit ();
}
function getDomainByLanguage($language) {
	$domainMapping = getconfig ( "domain_to_language" );

	if (! empty ( $domainMapping )) {
		$domainMapping = explode ( "\n", $domainMapping );
		for($i = 0; $i < count ( $domainMapping ); $i ++) {
			$line = trim ( $domainMapping [$i] );
			if (! empty ( $line )) {
				$line = explode ( "=>", $line );

				if (count ( $line ) > 1) {
					$line [0] = trim ( $line [0] );
					$line [1] = trim ( $line [1] );
					if (! empty ( $line [0] ) and ! empty ( $line [1] )) {

						if ($line [1] == $language) {
							return $line [0];
						}
					}
				}
			}
		}
	}
	return null;
}

// encodeURIComponent() is needed when working with accents
// If not used, generate a JS error in CKEDITOR link plugin
function encodeURIComponent($str) {
	$revert = array (
			'%21' => '!',
			'%2A' => '*',
			'%27' => "'",
			'%28' => '(',
			'%29' => ')'
	);
	return strtr ( rawurlencode ( $str ), $revert );
}
function setLanguageByDomain() {
	$domainMapping = getconfig ( "domain_to_language" );

	if (! empty ( $domainMapping )) {
		$domainMapping = explode ( "\n", $domainMapping );
		for($i = 0; $i < count ( $domainMapping ); $i ++) {
			$line = trim ( $domainMapping [$i] );
			if (! empty ( $line )) {
				$line = explode ( "=>", $line );
				if (count ( $line ) > 1) {
					$line [0] = trim ( $line [0] );
					$line [1] = trim ( $line [1] );

					if (! empty ( $line [0] ) and ! empty ( $line [1] )) {
						$domain = $_SERVER ["HTTP_HOST"];

						if ($line [0] == $domain and in_array ( $line [1], getAllLanguages () )) {
							$_SESSION ["language"] = $line [1];
							return true;
						}
					}
				}
			}
		}
	}
	return false;
}
function getCacheType() {
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

	return $cache_type;
}
function getOnlineUsers() {
	$users_online = db_query ( "SELECT * FROM " . tbname ( "users" ) . " WHERE last_action > " . (time () - 300) . " ORDER BY username" );
	$users = array ();
	while ( $row = db_fetch_object ( $users_online ) ) {
		array_push ( $users, $row->username );
	}
	return $users;
}

// get a config variable
function getconfig($key) {
    if(isset($GLOBALS['settings_cache'][$key])){
		    return $GLOBALS['settings_cache'][$key];
	}
	$env_key = "ulicms_" . $key;
	$env_var = getenv ( $env_key );
	if ($env_var)
		return $env_var;
	$ikey = db_escape ( $key );
	$query = db_query ( "SELECT value FROM " . tbname ( "settings" ) . " WHERE name='$key'" );
	if (db_num_rows ( $query ) > 0) {
		while ( $row = db_fetch_object ( $query ) ) {
		    $GLOBALS['settings_cache'][$key] = $row->value;
			return $row->value;
		}
	} else {
		    $GLOBALS['settings_cache'][$key] = false;
		return false;
	}
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
	if ($_SERVER ["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER ["SERVER_NAME"] . ":" . $_SERVER ["SERVER_PORT"] . $dirname;
	} else {
		$pageURL .= $_SERVER ["SERVER_NAME"] . $dirname;
	}
	return $pageURL;
}

if (! function_exists ( "get_host" )) {
	function get_host() {
		if ($host = $_SERVER ['HTTP_X_FORWARDED_HOST']) {
			$elements = explode ( ',', $host );

			$host = trim ( end ( $elements ) );
		} else {
			if (! $host = $_SERVER ['HTTP_HOST']) {
				if (! $host = $_SERVER ['SERVER_NAME']) {
					$host = ! empty ( $_SERVER ['SERVER_ADDR'] ) ? $_SERVER ['SERVER_ADDR'] : '';
				}
			}
		}

		// Remove port number from host
		$host = preg_replace ( '/:\d+$/', '', $host );

		return trim ( $host );
	}
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
	add_hook ( "before_clear_cache" );
	$cache_type = getconfig ( "cache_type" );
	// Es gibt zwei verschiedene Cache Modi
	// Cache_Lite und File
	// Cache_Lite leeren
	if ($cache_type === "cache_lite" and class_exists ( "Cache_Lite" )) {
		$Cache_Lite = new Cache_Lite ( $options );
		$Cache_Lite->clean ();
	} else {
		// File leeren
		if (is_admin_dir ())
			SureRemoveDir ( "../content/cache", false );
		else
			SureRemoveDir ( "content/cache", false );
	}

	if (function_exists ( "apc_clear_cache" )) {
		clearAPCCache ();
	}

	add_hook ( "after_clear_cache" );
}

// sind wir gerade im Adminordner?
function is_admin_dir() {
	return basename ( getcwd () ) === "admin";
}
function add_hook($name) {
	$modules = getAllModules ();
	for($hook_i = 0; $hook_i < count ( $modules ); $hook_i ++) {
		$file = getModulePath ( $modules [$hook_i] ) . $modules [$hook_i] . "_" . $name . ".php";
		if (file_exists ( $file )) {
			@include $file;
		}
	}
}
function register_action($name, $file) {
	global $actions;
	$modules = getAllModules ();
	$actions [$name] = $file;
	return $actions;
}
function remove_action($name) {
	global $actions;
	$retval = false;
	if (isset ( $action [$name] )) {
		unset ( $name );
		$retval = true;
	}
	return $retval;
}
function cms_release_year() {
	$v = new ulicms_version ();
	echo $v->getReleaseYear ();
}

// Check for Secure HTTP Connection (SSL)
function is_ssl() {
	return (! empty ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] !== 'off' || $_SERVER ['SERVER_PORT'] == 443);
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
	$locale = getconfig ( $var );
	if ($locale) {
		$locale = splitAndTrim ( $locale );
		array_unshift ( $locale, LC_ALL );
		@call_user_func_array ( "setlocale", $locale );
	} else {
		$locale = getconfig ( "locale" );
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
function getCurrentLanguage($current = true) {
	if ($current) {
		$query = db_query ( "SELECT * FROM " . tbname ( "content" ) . " WHERE systemname='" . get_requested_pagename () . "'" );

		if (db_num_rows ( $query ) > 0) {
			$fetch = db_fetch_object ( $query );
			return $fetch->language;
		}
	}

	if (isset ( $_SESSION ["language"] ))
		return basename ( $_SESSION ["language"] );
	else
		return basename ( getconfig ( "default_language" ) );
}

// Auf automatische aktualisieren prüfen.
// Rückgabewert: ein String oder False
function checkForUpdates() {
	include_once "../lib/file_get_contents_wrapper.php";
	$info = @file_get_contents_Wrapper ( UPDATE_CHECK_URL, true );
	if (! $info or trim ( $info ) === "")
		return false;
	else
		return $info;
}
function getThemeList() {
	return getThemesList ();
}
function getThemesList() {
	$pkg = new packageManager ();
	return $pkg->getInstalledPackages ( 'themes' );
}

// Make title url safe
if (! function_exists ( "cleanString" )) {
	function cleanString($string, $separator = '-') {
		$accents = array (
				'Š' => 'S',
				'š' => 's',
				'Ð' => 'Dj',
				'Ž' => 'Z',
				'ž' => 'z',
				'À' => 'A',
				'Á' => 'A',
				'Â' => 'A',
				'Ã' => 'A',
				'Ä' => 'A',
				'Å' => 'A',
				'Æ' => 'A',
				'Ç' => 'C',
				'È' => 'E',
				'É' => 'E',
				'Ê' => 'E',
				'Ë' => 'E',
				'Ì' => 'I',
				'Í' => 'I',
				'Î' => 'I',
				'Ï' => 'I',
				'Ñ' => 'N',
				'Ò' => 'O',
				'Ó' => 'O',
				'Ô' => 'O',
				'Õ' => 'O',
				'Ö' => 'O',
				'Ø' => 'O',
				'Ù' => 'U',
				'Ú' => 'U',
				'Û' => 'U',
				'Ü' => 'U',
				'Ý' => 'Y',
				'Þ' => 'B',
				'ß' => 'Ss',
				'à' => 'a',
				'á' => 'a',
				'â' => 'a',
				'ã' => 'a',
				'ä' => 'a',
				'å' => 'a',
				'æ' => 'a',
				'ç' => 'c',
				'è' => 'e',
				'é' => 'e',
				'ê' => 'e',
				'ë' => 'e',
				'ì' => 'i',
				'í' => 'i',
				'î' => 'i',
				'ï' => 'i',
				'ð' => 'o',
				'ñ' => 'n',
				'ò' => 'o',
				'ó' => 'o',
				'ô' => 'o',
				'õ' => 'o',
				'ö' => 'o',
				'ø' => 'o',
				'ù' => 'u',
				'ú' => 'u',
				'û' => 'u',
				'ý' => 'y',
				'ý' => 'y',
				'þ' => 'b',
				'ÿ' => 'y',
				'ƒ' => 'f',
				'Ä' => 'Ae',
				'ä' => 'ae',
				'Ö' => 'Oe',
				'ö' => 'oe',
				'Ü' => 'Ue',
				'ü' => 'ue',
				'ß' => 'ss'
		);
		$string = strtr ( $string, $accents );
		$string = strtolower ( $string );
		$string = preg_replace ( '/[^a-zA-Z0-9\s]/', '', $string );
		$string = preg_replace ( '{ +}', ' ', $string );
		$string = trim ( $string );
		$string = str_replace ( ' ', $separator, $string );
		return $string;
	}
}
function getTemplateDirPath($sub = "default") {
	if (is_admin_dir ())
		$templateDir = "../content/templates/";
	else
		$templateDir = "content/templates/";

	$templateDir = $templateDir . $sub . "/";
	return $templateDir;
}
function getModuleAdminSelfPath() {
	$self_path = $_SERVER ["REQUEST_URI"];
	$self_path = str_replace ( '"', '', $self_path );
	$self_path = str_replace ( "'", '', $self_path );
	return $self_path;
}
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
function get_protocol_and_domain() {
	return get_site_protocol () . get_domain ();
}
function get_domain() {
	return $_SERVER ['SERVER_NAME'];
}

// This Returns the current full URL
// for example: http://www.homepage.de/news.html?single=title
function getBaseFolderURL() {
	$s = empty ( $_SERVER ["HTTPS"] ) ? '' : ($_SERVER ["HTTPS"] == "on") ? "s" : "";
	$sp = strtolower ( $_SERVER ["SERVER_PROTOCOL"] );
	$protocol = substr ( $sp, 0, strpos ( $sp, "/" ) ) . $s;
	$port = ($_SERVER ["SERVER_PORT"] == "80") ? "" : (":" . $_SERVER ["SERVER_PORT"]);
	return trim($protocol . "://" . $_SERVER ['SERVER_NAME'] . $port .dirname($_SERVER ['REQUEST_URI']), "/");
}

// This Returns the current full URL
// for example: http://www.homepage.de/news.html?single=title
function getCurrentURL() {
	$s = empty ( $_SERVER ["HTTPS"] ) ? '' : ($_SERVER ["HTTPS"] == "on") ? "s" : "";
	$sp = strtolower ( $_SERVER ["SERVER_PROTOCOL"] );
	$protocol = substr ( $sp, 0, strpos ( $sp, "/" ) ) . $s;
	$port = ($_SERVER ["SERVER_PORT"] == "80") ? "" : (":" . $_SERVER ["SERVER_PORT"]);
	return $protocol . "://" . $_SERVER ['SERVER_NAME'] . $port . $_SERVER ['REQUEST_URI'];
}
function buildCacheFilePath($request_uri) {
	$language = $_SESSION ["language"];
	if (! $language) {
		$language = getconfig ( "default_language" );
	}
	$unique_identifier = $request_uri . $language . strbool ( is_mobile () );
	return "content/cache/" . md5 ( $unique_identifier ) . ".tmp";
}
function get_translation($name, $placeholders = array()) {
	$iname = strtoupper ( $name );
	foreach ( get_defined_constants () as $key => $value ) {
		if (startsWith ( $key, "TRANSLATION_" ) and $key == "TRANSLATION_" . $iname) {
			// Platzhalter ersetzen, diese können als assoziatives Array als zweiter Parameter
			// dem Funktionsaufruf mitgegeben werden
			foreach ( $placeholders as $placeholder => $replacement ) {
				$value = str_ireplace ( $placeholder, $replacement, $value );
			}
			return $value;
		}
	}
	return $name;
}
function translation($name, $placeholders = array()) {
	echo get_translation ( $name, $placeholders );
}
function translate($name, $placeholders = array()) {
	translation ( $name, $placeholders );
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
	if ($page === false)
		$page = get_requested_pagename ();

	if (startsWith ( $redirection, "#" )) {
		return $redirection;
	}

	if ($page === get_frontpage ())
		return "./";

	$seo_url = "";

	if (is_file ( "backend.php" ))
		$seo_url .= "../";
	$seo_url .= $page;
	$seo_url .= ".".trim($format, ".");
	return $seo_url;
}
function getModulePath($module) {
	// Frontend Directory
	if (is_file ( "cms-config.php" )) {
		$module_folder = "content/modules/";
	}  // Backend Directory
else {
		$module_folder = "../content/modules/";
	}
	$available_modules = Array ();
	return $module_folder . $module . "/";
}
function getModuleAdminFilePath($module) {
	return getModulePath ( $module ) . $module . "_admin.php";
}
function getModuleMainFilePath($module) {
	return getModulePath ( $module ) . $module . "_main.php";
}
function getModuleUninstallScriptPath($module) {
	return getModulePath ( $module ) . $module . "_uninstall.php";
}
function find_all_files($dir) {
	$root = scandir ( $dir );
	$result = array ();
	foreach ( $root as $value ) {
		if ($value === '.' || $value === '..') {
			continue;
		}
		if (is_file ( "$dir/$value" )) {
			$result [] = str_Replace("\\", "/", "$dir/$value");
			continue;
		}
		foreach ( find_all_files ( "$dir/$value" ) as $value ) {
		    $value = str_replace("\\", "/", $value);
			$result [] = $value;
		}
	}
	return $result;
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
function convertLineEndingsToLF($s) {
	// Normalize line endings using Global
	// Convert all line-endings to UNIX format
	$s = str_replace ( CRLF, LF, $s );
	$s = str_replace ( CR, LF, $s );
	// Don't allow out-of-control blank lines
	$s = preg_replace ( "/\n{2,}/", LF . LF, $s );
	return $s;
}
function isModuleInstalled($name) {
	return in_array ( $name, getAllModules () );
}
function getAllModules() {
	$pkg = new packageManager ();
	return $pkg->getInstalledPackages ( 'modules' );
}
function no_cache() {
	if (! defined ( "NO_CACHE" )) {
		define ( "NO_CACHE", true );
	}
}
function no_anti_csrf() {
	if (! defined ( "NO_ANTI_CSRF" )) {
		define ( "NO_ANTI_CSRF", true );
	}
}
function replaceAudioTags($txt) {
	// Ich weiß, dass das eigentlich einfacher mit einem regulären Ausdruck geht, aber ich kann keine reguläre Ausdrücke.
	// Reguläre Ausdrücke sehen für mich so aus, als wäre eine Katze über die Tastatur gelaufen.
	$contains = strpos ( $txt, "[audio id=" ) !== FALSE;

	if ($contains) {
		$query = db_query ( "select id, ogg_file, mp3_file from " . tbname ( "audio" ) . " order by id" );

		while ( $row = db_fetch_object ( $query ) ) {
			$code1 = "[audio id=\"" . $row->id . "\"]";
			$code2 = "[audio id=$quot;" . $row->id . "$quot;]";
			$code3 = "[audio id=" . $row->id . "]";
			if (! empty ( $row->mp3_file ))
				$preferred = $row->mp3_file;
			else
				$preferred = $row->ogg_file;

			$html = '<audio controls>';
			if (! empty ( $row->mp3_file )) {
				$html .= '<source src="content/audio/' . htmlspecialchars ( $row->mp3_file ) . '" type="audio/mp3">';
			}
			if (! empty ( $row->ogg_file )) {
				$html .= '<source src="content/audio/' . htmlspecialchars ( $row->ogg_file ) . '" type="audio/ogg">';
			}
			$html .= TRANSLATION_NO_HTML5;
			if (! empty ( $row->mp3_file ) or ! empty ( $row->ogg_file )) {
				$html .= '<br/>
		<a href="content/audio/' . $preferred . '">' . TRANSLATION_DOWNLOAD_AUDIO_INSTEAD . '</a>';
			}
			$html .= '</audio>';
			$txt = str_replace ( $code1, $html, $txt );
			$txt = str_replace ( $code2, $html, $txt );
			$txt = str_replace ( $code3, $html, $txt );
		}
	}

	return $txt;
}
function replaceVideoTags($txt) {
	// Ich weiß, dass das eigentlich einfacher mit einem regulären Ausdruck geht, aber ich kann keine reguläre Ausdrücke.
	// Reguläre Ausdrücke sehen für mich so aus, als wäre eine Katze über die Tastatur gelaufen.
	$contains = strpos ( $txt, "[video id=" ) !== FALSE;

	if ($contains) {
		$query = db_query ( "select id, ogg_file, webm_file, mp4_file, width, height from " . tbname ( "videos" ) . " order by id" );

		while ( $row = db_fetch_object ( $query ) ) {
			$code1 = "[video id=\"" . $row->id . "\"]";
			$code2 = "[video id=$quot;" . $row->id . "$quot;]";
			$code3 = "[video id=" . $row->id . "]";
			if (! empty ( $row->mp4_file ))
				$preferred = $row->mp4_file;
			else if (! empty ( $row->ogg_file ))
				$preferred = $row->ogg_file;
			else
				$preferred = $row->webm_file;

			$html = '<video width="' . $row->width . '" height="' . $row->height . '" controls>';
			if (! empty ( $row->mp4_file )) {
				$html .= '<source src="content/videos/' . htmlspecialchars ( $row->mp4_file ) . '" type="video/mp4">';
			}
			if (! empty ( $row->ogg_file )) {
				$html .= '<source src="content/videos/' . htmlspecialchars ( $row->ogg_file ) . '" type="video/ogg">';
			}
			if (! empty ( $row->webm_file )) {
				$html .= '<source src="content/videos/' . htmlspecialchars ( $row->webm_file ) . '" type="video/webm">';
			}
			$html .= TRANSLATION_NO_HTML5;
			if (! empty ( $row->mp4_file ) or ! empty ( $row->ogg_file ) or ! empty ( $row->webm_file )) {
				$html .= '<br/>
		<a href="content/videos/' . $preferred . '">' . TRANSLATION_DOWNLOAD_VIDEO_INSTEAD . '</a>';
			}
			$html .= '</video>';
			$txt = str_replace ( $code1, $html, $txt );
			$txt = str_replace ( $code2, $html, $txt );
			$txt = str_replace ( $code3, $html, $txt );
		}
	}
	return $txt;
}

// replace Shortcodes with modules
function replaceShortcodesWithModules($string, $replaceOther = true) {
	if ($replaceOther) {
		$string = str_replace ( '[title]', get_title (), $string );
		ob_start ();
		logo ();
		$string = str_replace ( '[logo]', ob_get_clean (), $string );
		ob_start ();
		motto ();
		$string = str_replace ( '[motto]', ob_get_clean (), $string );
		ob_start ();
		motto ();
		$string = str_replace ( '[slogan]', ob_get_clean (), $string );
		$current_page = get_page ();
		$string = str_replace ( '[category]', get_category (), $string );
		$string = str_replace ( '[csrf_token_html]', get_csrf_token_html (), $string );
		// [tel] Links for tel Tags
		$string = preg_replace ( '/\[tel\]([^\[\]]+)\[\/tel\]/i', '<a href="tel:$1" class="tel">$1</a>', $string );
		$string = preg_replace ( '/\[skype\]([^\[\]]+)\[\/skype\]/i', '<a href="skye:$1?call" class="skype">$1</a>', $string );
	}
	$allModules = getAllModules ();
	for($i = 0; $i <= count ( $allModules ); $i ++) {
		$thisModule = $allModules [$i];
		$stringToReplace1 = '[module="' . $thisModule . '"]';
		$stringToReplace2 = '[module=&quot;' . $thisModule . '&quot;]';

		$module_mainfile_path = getModuleMainFilePath ( $thisModule );

		if (is_file ( $module_mainfile_path ) and (strstr ( $string, $stringToReplace1 ) or strstr ( $string, $stringToReplace2 ))) {
			require_once $module_mainfile_path;
			if (function_exists ( $thisModule . "_render" )) {
				$html_output = call_user_func ( $thisModule . "_render" );
			} else {
				$html_output = "<p class='ulicms_error'>Das Modul " . $thisModule . " konnte nicht geladen werden.</p>";
			}
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
		return "[" . TRANSLATION_NONE . "]";
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
function getAllPages($lang = null, $order = "systemname", $exclude_hash_links = true) {
	if (! $lang) {
		$query = db_query ( "SELECT * FROM `" . tbname ( "content" ) . "` WHERE `deleted_at` IS NULL ORDER BY $order" );
	} else {

		$query = db_query ( "SELECT * FROM `" . tbname ( "content" ) . "` WHERE `deleted_at` IS NULL AND language ='" . db_escape ( $lang ) . "' ORDER BY $order" );
	}
	$returnvalues = Array ();
	while ( $row = db_fetch_assoc ( $query ) ) {
		if (! ($exclude_hash_links and startsWith ( $row ["redirection"], "#" ))) {
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
function getAllLanguages() {
	$query = db_query ( "SELECT language_code FROM `" . tbname ( "languages" ) . "` ORDER BY language_code" );
	$returnvalues = Array ();
	while ( $row = db_fetch_object ( $query ) ) {
		array_push ( $returnvalues, $row->language_code );
	}
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
	if ($_SERVER ["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER ["SERVER_NAME"] . ":" . $_SERVER ["SERVER_PORT"] . $dirname;
	} else {
		$pageURL .= $_SERVER ["SERVER_NAME"] . $dirname;
	}
	return $pageURL;
}
function file_extension($filename) {
	$ext = explode ( ".", $filename );
	$ext = end ( $ext );
	return $ext;
}

// Remove an configuration variable
function deleteconfig($key) {
	$key = db_escape ( $key );
	db_query ( "DELETE FROM " . tbname ( "settings" ) . " WHERE name='$key'" );
	return db_affected_rows () > 0;
}

// Set a configuration Variable;
function setconfig($key, $value) {
	$query = db_query ( "SELECT id FROM " . tbname ( "settings" ) . " WHERE name='$key'" );

	if (db_num_rows ( $query ) > 0) {
		db_query ( "UPDATE " . tbname ( "settings" ) . " SET value='$value' WHERE name='$key'" );
	} else {

		db_query ( "INSERT INTO " . tbname ( "settings" ) . " (name, value) VALUES('$key', '$value')" );
	}

	if(isset($GLOBALS['settings_cache'][$key])){
	    unset($GLOBALS['settings_cache'][$key]);
	}
}
function is__writable($path) {
	if ($path {strlen ( $path ) - 1} == '/')

		return is__writable ( $path . uniqid ( mt_rand () ) . '.tmp' );

	elseif (file_exists ( $path ) && preg_match ( '/\.tmp/', $path )) {

		return is_writable ( $path );
	} else
		return false; // Or return error - invalid path...
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
			"none"
	);
	$additional_menus = getconfig ( "additional_menus" );

	if ($additional_menus) {
		$additional_menus = explode ( ";", $additional_menus );
		foreach ( $additional_menus as $m ) {
			array_push ( $menus, $m );
		}
	}
	if($only_used){
	$used = get_all_used_menus();
    $new_menus = array();
	for($i=0; $i <= count($menus); $i++){
	   if(in_array($menus[$i], $used)){
	      $new_menus[] = $menus[$i];
	   }
	}
	$menus = $new_menus;
	}
	sort($menus);
	return $menus;
}

// Check if site contains a module
function containsModule($page = null, $module = false) {
	if (is_null ( $page ))
		$page = get_requested_pagename ();

	$query = db_query ( "SELECT content FROM " . tbname ( "content" ) . " WHERE systemname = '" . db_escape ( $page ) . "'" );
	$dataset = db_fetch_assoc ( $query );
	$content = $dataset ["content"];
	$content = str_replace ( "&quot;", "\"", $content );
	if ($module)
		return preg_match ( "/\[module=\"" . preg_quote ( $module ) . "\"\]/", $content );
	else
		return preg_match ( "/\[module=\".+\"\]/", $content );
}
function page_has_html_file($page) {
	$query = db_query ( "SELECT `html_file` FROM " . tbname ( "content" ) . " WHERE systemname = '" . db_escape ( $page ) . "'" );
	$dataset = db_fetch_assoc ( $query );
	$html_file = $dataset ["html_file"];
	if (empty ( $html_file ) or is_null ( $html_file ))
		return null;
	$html_file = dirname ( __file__ ) . "/content/files/" . $html_file;
	if (! endsWith ( $html_file, ".html" ) && ! endsWith ( $html_file, ".htm" )) {
		$html_file = $html_file . ".html";
	}
	return $html_file;
}

// API-Aufruf zur Deinstallation eines Moduls
// Ruft uninstall Script auf, falls vorhanden
// Löscht anschließend den Ordner modules/$name
// @TODO dies in die PackageManager Klasse verschieben
function uninstall_module($name, $type = "module") {
	$acl = new ACL ();
	// Nur Admins können Module löschen
	if (! $acl->hasPermission ( "install_packages" ))
		return false;

	$name = trim ( $name );
	$name = basename ( $name );
	$name = trim ( $name );

	// Verhindern, dass der Modulordner oder gar das ganze
	// CMS gelöscht werden kann
	if ($name == "." or $name == ".." or empty ( $name ))
		return false;

	if ($type === "module") {
		$moduleDir = getModulePath ( $name );
		// Modul-Ordner entfernen
		if (is_dir ( $moduleDir )) {
			$uninstall_script = getModuleUninstallScriptPath ( $name );
			// Uninstall Script ausführen, sofern vorhanden
			if (is_file ( $uninstall_script ))
				include $uninstall_script;
			sureRemoveDir ( $moduleDir, true );
			clearCache ();
			return ! is_dir ( $moduleDir );
		}
	} else if ($type === "theme") {
		$cTheme = getconfig ( "theme" );
		$allThemes = getThemeList ();
		if (in_array ( $name, $allThemes ) and $cTheme !== $name) {
			$theme_path = getTemplateDirPath ( $name );
			sureRemoveDir ( $theme_path, true );
			clearCache ();
			return ! is_dir ( $theme_path );
		}
	}

	return false;
}

// Ist der User eingeloggt
function is_logged_in() {
	return isset ( $_SESSION ["logged_in"] );
}

// @Deprecated
// Gehörte noch zur alten Hierarchie-basierten Rechteverwaltung
function has_permissions($mod) {
	if (! isset ( $_SESSION ["group"] ))
		return false;
	return $_SESSION ["group"] >= $mod;
}

// Alias für is_logged_in
function logged_in() {
	return is_logged_in ();
}

// Tabellenname zusammensetzen
function tbname($name) {
	require_once "cms-config.php";
	$config = new config ();
	return $config->db_prefix . $name;
}

// returns version number of UliCMS Core
function cms_version() {
	require_once "version.php";
	$v = new ulicms_version ();
	return implode ( ".", $v->getInternalVersion () );
}

// 21. Februar 2015
// Nutzt nun die Klasse Mobile_Detect
function is_mobile() {
	$detect = new Mobile_Detect ();
	$result = $detect->isMobile ();
	if(getconfig("no_mobile_design_on_tablet") and $result and $detect->isTablet()){
           $result = false;
        }
  $result = apply_filter ( $result, "is_mobile" );
  return $result;
}
function func_enabled($func) {
	$disabled = explode ( ',', ini_get ( 'disable_functions' ) );
	foreach ( $disabled as $disableFunction ) {
		$is_disabled [] = trim ( $disableFunction );
	}
	if (in_array ( $func, $is_disabled )) {
		$it_is_disabled ["m"] = $func . '() has been disabled for security reasons in php.ini';
		$it_is_disabled ["s"] = 0;
	} else {
		$it_is_disabled ["m"] = $func . '() is allow to use';
		$it_is_disabled ["s"] = 1;
	}
	return $it_is_disabled;
}
function is_admin() {
	$retval = false;
	$user_id = get_user_id ();
	if (! $user_id) {
		$retval = false;
	} else {
		$query = db_query ( "SELECT `admin` FROM " . tbname ( "users" ) . " where id = " . $user_id . " and admin = 1" );
		$retval = db_num_rows ( $query );
	}

	return $retval;
}

// This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
function convertPHPSizeToBytes($sSize) {
	if (is_numeric ( $sSize )) {
		return $sSize;
	}
	$sSuffix = substr ( $sSize, - 1 );
	$iValue = substr ( $sSize, 0, - 1 );
	switch (strtoupper ( $sSuffix )) {
		case 'P' :
			$iValue *= 1024;
		case 'T' :
			$iValue *= 1024;
		case 'G' :
			$iValue *= 1024;
		case 'M' :
			$iValue *= 1024;
		case 'K' :
			$iValue *= 1024;
			break;
	}
	return $iValue;
}
function getMaximumFileUploadSize() {
	return min ( convertPHPSizeToBytes ( ini_get ( 'post_max_size' ) ), convertPHPSizeToBytes ( ini_get ( 'upload_max_filesize' ) ) );
}

require_once "users_api.php";
require_once "legacy.php";
