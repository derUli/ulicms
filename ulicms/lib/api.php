<?php

// TODO: this file is way too long
// refactor this into multiple files

declare(strict_types=1);

use UliCMS\Security\PermissionChecker;
use Negotiation\LanguageNegotiator;
use UliCMS\Constants\ModuleEventConstants;
use UliCMS\Models\Content\Types\DefaultContentTypes;
use UliCMS\Utils\CacheUtil;

function idefine($key, $value): bool {
	$key = strtoupper($key);
	if (!defined($key)) {
		define($key, $value);
		return true;
	}
	return false;
}

function var_dump_str(): string {
	$argc = func_num_args();
	$argv = func_get_args();

	if ($argc > 0) {
		ob_start();
		call_user_func_array('var_dump', $argv);
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}

	return '';
}

function remove_prefix(string $text, string $prefix): string {
	if (startsWith($text, $prefix)) {
		$text = substr($text, strlen($prefix));
	}
	return $text;
}

function remove_suffix(string $text, string $suffix): string {
	if (endsWith($text, $suffix)) {
		$text = substr($text, 0, strlen($text) - strlen($suffix));
	}
	return $text;
}

// TODO: refactor this into a "polyfill.php" file
// replacement for the each() function which is deprecated since PHP 7.2.0
// used by kcfinder
function myEach(&$arr) {
	$key = key($arr);
	$result = ($key === null) ? false : [
		$key,
		current($arr),
		'key' => $key,
		'value' => current($arr)
	];
	next($arr);
	return $result;
}

if (!function_exists("each")) {

	function each(&$arr) {
		return myEach($arr);
	}

}

function bool2YesNo(
		bool $value,
		?string $yesString = null,
		?string $noString = null
): string {
	if (!$yesString) {
		$yesString = get_translation("yes");
	}
	if (!$noString) {
		$noString = get_translation("no");
	}
	return ($value ? $yesString : $noString);
}

// like json_encode() but human readable
function json_readable_encode($in, $indent = 0): string {
	$_myself = __FUNCTION__;
	$_escape = function ($str) {
		return preg_replace("!([\b\t\n\r\f\"\\'])!", "\\\\\\1", $str);
	};

	$out = '';

	foreach ($in as $key => $value) {
		$out .= str_repeat("\t", $indent + 1);
		$out .= "\"" . $_escape((string) $key) . "\": ";

		if (is_object($value) || is_array($value)) {
			$out .= "\n";
			$out .= $_myself($value, $indent + 1);
		} elseif (is_bool($value)) {
			$out .= $value ? 'true' : 'false';
		} elseif (is_null($value)) {
			$out .= 'null';
		} elseif (is_string($value)) {
			$out .= "\"" . $_escape($value) . "\"";
		} else {
			$out .= $value;
		}

		$out .= ",\n";
	}

	if (!empty($out)) {
		$out = substr($out, 0, - 2);
	}

	$out = str_repeat("\t", $indent) . "{\n" . $out;
	$out .= "\n" . str_repeat("\t", $indent) . "}";

	return $out;
}

function add_translation($key, $value) {
	register_translation($key, $value);
}

function register_translation($key, $value) {
	$key = strtoupper($key);
	if (!startsWith($key, "TRANSLATION_")) {
		$key = "TRANSLATION_" . $key;
	}
	idefine($key, $value);
}

// Get a subset of an associative array by providing the keys.
function array_keep(array $array, array $keys): array {
	return array_intersect_key($array, array_fill_keys($keys, null));
}

function getAllUsedLanguages(): array {
	$languages = [];
	$sql = "select language from `{prefix}content` where active = 1 "
			. "group by language order by language";
	$result = Database::query($sql, true);
	while ($row = Database::fetchobject($result)) {
		$languages[] = $row->language;
	}
	return $languages;
}

// prepares a text / code for html output
// replaces new lines with <br> tags
function preparePlainTextforHTMLOutput($text): string {
	return UliCMS\HTML\text($text);
}

function get_action(): string {
	return BackendHelper::getAction();
}

function getStringLengthInBytes(string $data): int {
	return ini_get('mbstring.func_overload') ?
			mb_strlen($data, '8bit') : strlen($data);
}

function set_format(string $format): void {
	$_GET["format"] = trim($format, ".");
}

function get_format(): string {
	return is_present($_GET["format"]) ? $_GET["format"] : "html";
}

function get_jquery_url(): string {
	$url = "node_modules/jquery/dist/jquery.min.js";
	$url = apply_filter($url, "jquery_url");
	return $url;
}

function get_prefered_language($priorities, $http_accept_language) {
	$negotiator = new LanguageNegotiator();
	return $negotiator->getBest($http_accept_language, $priorities)->getType();
}

function get_all_used_menus(): array {
	$retval = [];
	$result = db_query("select menu from " . tbname("content") .
			" group by menu");
	while ($row = db_fetch_object($result)) {
		$retval[] = $row->menu;
	}
	return $retval;
}

function get_shortlink($id = null): ?string {
	$shortlink = null;
	$id = $id ? $id : get_ID();

	if ($id) {
		$shortlink = getBaseFolderURL() . "/?goid=" . $id;
		$shortlink = apply_filter($shortlink, "shortlink");
	}

	return $shortlink;
}

function get_canonical(): string {
	$canonical = getBaseFolderURL() . "/";
	if (!is_frontpage()) {
		$canonical .= buildSEOUrl();
	}

	if (containsModule(null, "blog")) {
		if (isset($_GET["single"])) {
			$canonical .= "?single=" . _esc($_GET["single"]);
		} else if (isset($_GET["limit"])) {
			$canonical .= "?limit=" . intval($_GET["limit"]);
		}
	}
	$canonical = apply_filter($canonical, "canonical");
	return $canonical;
}

function get_lang_config(string $name, string $lang): ?string {
	$retval = null;
	$config = Settings::get($name . "_" . $lang);
	if ($config) {
		$retval = $config;
	} else {
		$config = Settings::get($name);
	}
	return $config ? $config : null;
}

/**
 * Gravatar support was removed due to legal issues with the GDPR.
 * This method is still called get_gravatar() for compatiblity reasons
 * If there is a registered user with the email address returns his avatar image file
 * else returns the no avatar placeholder image
 *
 */
function get_gravatar(
		string $email,
		int $s = 80,
		string $d = 'mm',
		string $r = 'g',
		bool $img = false,
		array $atts = []
): string {
	//
	$url = ModuleHelper::getBaseUrl("/admin/gfx/no_avatar.png");

	// If there is a user with this email address return it's avatar
	$user = new User();
	$user->loadByEmail($email);
	if ($user->hasProcessedAvatar()) {
		$url = $user->getAvatar();
	}

	$html = "";
	if ($img) {
		$html = '<img src="' . $url . '"';
		foreach ($atts as $key => $val) {
			$html .= ' ' . $key . '="' . $val . '"';
		}
		$html .= ' />';
	}
	return $img ? $html : $url;
}

// Random string generieren (für Passwort)
function rand_string(int $length): string {
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	return substr(str_shuffle($chars), 0, $length);
}

function getLanguageFilePath(string $lang = "de"): string {
	return ULICMS_ROOT . "/lang/" . $lang . ".php";
}

// Gibt den für den derzeit eingeloggten User eingestellten HTML-Editor aus.
// Wenn der Anwender nicht eingeloggt ist return null
function get_html_editor(): ?string {
	$user_id = get_user_id();

	if (!$user_id) {
		return "ckeditor";
	}

	$user = new User($user_id);
	return $user->getHTMLEditor();
}

function check_form_timestamp(): void {
	if (Settings::get("spamfilter_enabled") != "yes") {
		return;
	}

	$original_timestamp = Request::getVar("form_timestamp", 0, "int");
	$min_time_to_fill_form = Settings::get("min_time_to_fill_form", "int");
	if (time() - $original_timestamp < $min_time_to_fill_form) {
		setconfig(
				"contact_form_refused_spam_mails",
				getconfig("contact_form_refused_spam_mails") + 1
		);
		HTMLResult("Spam detected based on timestamp.", 400);
	}
}

function getFieldsForCustomType(string $type): array {
	$fields = [];
	$modules = getAllModules();
	foreach ($modules as $module) {
		$custom_types = getModuleMeta($module, "custom_types");
		if (!$custom_types) {
			continue;
		}
		foreach ($custom_types as $key => $value) {
			if ($key === $type) {
				foreach ($value as $field) {
					$fields[] = $field;
				}
			}
		}
	}
	return $fields;
}

function get_used_post_types(): array {
	$result = Database::query("select `type` from {prefix}content "
					. "group by `type`", true);
	$types = get_available_post_types();
	$used_types = [];
	$return_types = [];
	while ($row = Database::fetchObject($result)) {
		$used_types[] = $row->type;
	}
	foreach ($types as $type) {
		if (faster_in_array($type, $used_types)) {
			$return_types[] = $type;
		}
	}
	return $return_types;
}

function get_available_post_types(): array {
	$types = DefaultContentTypes::getAll();
	$types = array_keys($types);
	return $types;
}

// Schriftgrößen zurückgeben
function getFontSizes(): array {
	global $sizes;
	$sizes = [];
	for ($i = 6; $i <= 80; $i ++) {
		$sizes[] = $i . "px";
	}
	do_event("custom_font_sizes");

	$sizes = apply_filter($sizes, "font_sizes");
	return $sizes;
}

function getModuleMeta($module, $attrib = null) {
	$metadata_file = ModuleHelper::buildModuleRessourcePath(
					$module,
					"metadata.json",
					true
	);
	if (!file_exists($metadata_file) or is_dir($metadata_file)) {
		return null;
	}

	$data = file_get_contents($metadata_file);
	$json = json_decode($data, true);

	if ($attrib and ! isset($json[$attrib])) {
		return null;
	}
	return $attrib ? $json[$attrib] : $json;
}

function getThemeMeta(string $theme, string $attrib = null) {
	$retval = null;
	$metadata_file = getTemplateDirPath($theme, true) . "metadata.json";
	if (file_exists($metadata_file)) {
		$data = !Vars::get("theme_{$theme}_meta") ?
				file_get_contents($metadata_file) : Vars::get("theme_{$theme}_meta");

		if (is_string($data)) {
			$data = json_decode($data, true);
		}
		Vars::set("theme_{$theme}_meta", $data);
		if ($attrib != null) {
			if (isset($data[$attrib])) {
				$retval = $data[$attrib];
			}
		} else {
			$retval = $data;
		}
	}
	return $retval;
}

function getLanguageNameByCode(string $code): string {
	$result = db_query(
			"SELECT name FROM `" . tbname("languages") .
			"` WHERE language_code = '" . db_escape($code) . "'"
	);
	$retval = $code;
	if (db_num_rows($result) > 0) {
		$dataset = db_fetch_object($result);
		$retval = $dataset->name;
	}

	return $retval;
}

function getAvailableBackendLanguages(): array {
	$langdir = ULICMS_ROOT . "/lang/";
	$list = scandir($langdir);
	sort($list);
	$retval = [];
	for ($i = 0; $i < count($list); $i ++) {
		if (endsWith($list[$i], ".php")) {
			$retval[] = basename($list[$i], ".php");
		}
	}

	return $retval;
}

function getSystemLanguage(): string {
	if (isset($_SESSION["system_language"])) {
		$lang = $_SESSION["system_language"];
	} else if (isset($_SESSION["language"])) {
		$lang = $_SESSION["language"];
	} else if (Settings::get("system_language")) {
		$lang = Settings::get("system_language");
	} else {
		$lang = "de";
	}
	if (!file_exists(getLanguageFilePath($lang))) {
		$lang = "de";
	}
	return $lang;
}

function getDomainByLanguage($givenLanguage): ?string {
	$domainMapping = Settings::get("domain_to_language");
	$domainMapping = Settings::mappingStringToArray($domainMapping);
	foreach ($domainMapping as $domain => $language) {
		if ($givenLanguage == $language) {
			return $domain;
		}
	}
	return null;
}

function getLanguageByDomain($givenDomain): ?string {
	$domainMapping = Settings::get("domain_to_language");
	$domainMapping = Settings::mappingStringToArray($domainMapping);
	foreach ($domainMapping as $domain => $language) {
		if ($givenDomain == $domain) {
			return $language;
		}
	}
	return null;
}

function setLanguageByDomain(): bool {
	$domainMapping = Settings::get("domain_to_language");
	$domainMapping = Settings::mappingStringToArray($domainMapping);
	foreach ($domainMapping as $domain => $language) {
		$givenDomain = $_SERVER["HTTP_HOST"];
		if ($domain == $givenDomain
				and faster_in_array(
						$language,
						getAllLanguages())
		) {
			$_SESSION["language"] = $language;
			return true;
		}
	}
	return false;
}

function getOnlineUsers(): array {
	return getUsersOnline();
}

function rootDirectory(): string {
	return ModuleHelper::getBaseUrl();
}

// Alternative PHP Cache leeren, sofern installiert und aktiv
function clearAPCCache(): bool {
	if (!function_exists("apc_clear_cache")) {
		return false;
	}
	apc_clear_cache();
	apc_clear_cache('user');
	apc_clear_cache('opcode');
	return true;
}

// Alle Caches leeren
// Sowohl den Seiten-Cache, den Download/Paketmanager Cache
// als auch den APC Bytecode Cache
function clearCache(): void {
	CacheUtil::clearCache();
}

// DEPRECATED:
// This function may be removed in future releases of UliCMS
// Use do_event()
function add_hook(
		string $name,
		string $runs = ModuleEventConstants::RUNS_ONCE
): void {
	trigger_error("add_hook() is deprecated. Please use do_event().",
			E_USER_DEPRECATED);
	do_event($name, $runs);
}

function do_event(
		string $name,
		string $runs = ModuleEventConstants::RUNS_ONCE
) {
// don't run this code on kcfinder page (media)
// since the "Path" class has a naming conflict with the same named
// class of KCFinder
	if (defined("KCFINDER_PAGE")) {
		return;
	}
	$modules = getAllModules();
	$disabledModules = Vars::get("disabledModules");
	for ($hook_i = 0; $hook_i < count($modules); $hook_i ++) {
		if (faster_in_array($modules[$hook_i], $disabledModules)) {
			continue;
		}
		$file1 = getModulePath($modules[$hook_i], true) .
				$modules[$hook_i] . "_" . $name . ".php";
		$file2 = getModulePath($modules[$hook_i], true) .
				"hooks/" . $name . ".php";
		$main_class = getModuleMeta($modules[$hook_i], "main_class");
		$controller = null;
		if ($main_class) {
			$controller = ControllerRegistry::get($main_class);
		}
		ob_start();
		$escapedName = ModuleHelper::underscoreToCamel($name);
		if ($controller and method_exists($controller, $escapedName)) {
			echo $controller->$escapedName();
		} else if (file_exists($file1)) {
			if ($runs === ModuleEventConstants::RUNS_MULTIPLE) {
				require $file1;
			} else {
				require_once $file1;
			}
		} else if (file_exists($file2)) {

			if ($runs === ModuleEventConstants::RUNS_MULTIPLE) {
				require $file1;
			} else {
				require_once $file2;
			}
		}
		echo optimizeHtml(ob_get_clean());
	}
}

function splitAndTrim(string $str): array {
	return array_map('trim', explode(";", $str));
}

function setLocaleByLanguage(): array {
	$locale = null;
	if (is_admin_dir()) {
		$var = "locale_" . db_escape($_SESSION["system_language"]);
	} else {
		$var = "locale_" . db_escape($_SESSION["language"]);
	}
	$locale = Settings::get($var);
	if ($locale) {
		$locale = splitAndTrim($locale);
		array_unshift($locale, LC_ALL);
		@call_user_func_array("setlocale", $locale);
	} else {
		$locale = Settings::get("locale");
		if ($locale) {

			$locale = splitAndTrim($locale);
			array_unshift($locale, LC_ALL);
			@call_user_func_array("setlocale", $locale);
		}
	}
	return $locale;
}

// Returns the language code of the current language
// If $current is true returns language of the current page
// else it returns $_SESSION["language"];
function getCurrentLanguage($current = false): string {
	if (Vars::get("current_language_" . strbool($current))) {
		return Vars::get("current_language_" . strbool($current));
	}
	if ($current) {
		$result = db_query("SELECT language FROM " . tbname("content") .
				" WHERE slug='" . get_requested_pagename() . "'");
		if (db_num_rows($result) > 0) {
			$dataset = db_fetch_object($result);
			$language = $dataset->language;
			Vars::set("current_language_" . strbool($current), $language);
		}
	}

	if (isset($_SESSION["language"])) {
		return basename($_SESSION["language"]);
	} else {
		return basename(Settings::get("default_language"));
	}
}

function getAllThemes(): array {
	$pkg = new PackageManager();
	return $pkg->getInstalledPackages('themes');
}

function getTemplateDirPath(
		string $sub = "default",
		bool $abspath = false):
 string {
	if ($abspath) {
		$templateDir = Path::resolve(
						"ULICMS_DATA_STORAGE_ROOT/content/templates/"
				) . "/";
	} else if (ULICMS_ROOT != ULICMS_DATA_STORAGE_ROOT
			and defined("ULICMS_DATA_STORAGE_URL")) {
		$templateDir = Path::resolve(
						"ULICMS_DATA_STORAGE_URL/content/templates"
				) . "/";
	} else if (is_admin_dir()) {
		$templateDir = "../content/templates/";
	} else {
		$templateDir = "content/templates/";
	}

	$templateDir = $templateDir . $sub . "/";
	return $templateDir;
}

// XXX: What's the meaning of this method?
// is this method mandatory or is there an other method
// which can be used as replacement?
function getModuleAdminSelfPath(): string {
	return _esc(get_request_uri());
}

// this magic method replaces html num entities with the character
// used in PlainTextCreator
function replace_num_entity(string $ord) {
	$ord = $ord[1];
	if (preg_match('/^x([0-9a-f]+)$/i', $ord, $match)) {
		$ord = hexdec($match[1]);
	} else {
		$ord = intval($ord);
	}

	$no_bytes = 0;
	$byte = [];

	if ($ord < 128) {
		return chr($ord);
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
		case 2: {
				$prefix = array(
					31,
					192
				);
				break;
			}
		case 3: {
				$prefix = array(
					15,
					224
				);
				break;
			}
		case 4: {
				$prefix = array(
					7,
					240
				);
			}
	}

	for ($i = 0; $i < $no_bytes; $i ++) {
		$byte[$no_bytes - $i - 1] = (
				($ord & (63 * pow(2, 6 * $i))) / pow(2, 6 * $i)) &
				63 | 128;
	}

	$byte[0] = ($byte[0] & $prefix[0]) | $prefix[1];

	$ret = '';
	for ($i = 0; $i < $no_bytes; $i ++) {
		$ret .= chr($byte[$i]);
	}

	return $ret;
}

// TODO: this code works but looks like garbage
// rewrite this method
function getBaseFolderURL(?string $suffix = null): string {
	$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ?
			"s" : "";
	$sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
	$protocol = substr($sp, 0, strpos($sp, "/")) . $s;
	$port = ($_SERVER["SERVER_PORT"] == "80"
			or $_SERVER["SERVER_PORT"] == "443") ?
			"" : (":" . $_SERVER["SERVER_PORT"] );
	$path = basename(dirname($_SERVER['REQUEST_URI'])) == "" ?
			$_SERVER['REQUEST_URI'] : dirname($_SERVER['REQUEST_URI']);
	$suffix = $suffix ?
			str_replace("\\", "/", $suffix) : str_replace("\\", "/", $path);
	return trim(
			rtrim(
					$protocol . "://"
					. $_SERVER['HTTP_HOST'] . $port
					.
					$suffix), "/");
}

// This Returns the current full URL
// for example: http://www.homepage.de/news.html?single=title
function getCurrentURL(): string {
	return getBaseFolderURL(get_request_uri());
}

/**
 * Generate path to Page
 * Argumente
 * String $page (Slug)
 * Rückgabewert String im Format
 * ../seite.html
 * bzw.
 * seite.html;
 */
function buildSEOUrl(
		?string $page = null,
		?string $redirection = null,
		?string $format = null
) {
	if ($redirection) {
		return $redirection;
	}
	if (!$page) {
		$page = get_requested_pagename();
	}

	if (!$format) {
		$format = get_format() ? get_format() : "html";
	}

	if ($page === get_frontpage()) {
		return "./";
	}

	$seo_url = "";

	if (file_exists("backend.php")) {
		$seo_url .= "../";
	}
	$seo_url .= $page;
	$seo_url .= "." . trim($format, ".");
	return $seo_url;
}

function getModulePath($module, $abspath = false): string {
	if ($abspath) {
		return Path::resolve(
						"ULICMS_DATA_STORAGE_ROOT/content/modules/$module"
				) . "/";
	}
	if (ULICMS_ROOT == ULICMS_DATA_STORAGE_ROOT and ! defined("ULICMS_DATA_STORAGE_URL")) {
// Frontend Directory
		if (file_exists("CMSConfig.php")) {
			$module_folder = "content/modules/";
		} // Backend Directory
		else {
			$module_folder = "../content/modules/";
		}
	} else {
		$module_folder = Path::resolve(
						"ULICMS_DATA_STORAGE_URL/content/modules"
				) . "/";
	}

	return $module_folder . $module . "/";
}

function getModuleAdminFilePath($module): string {
	return getModulePath($module, true) . $module . "_admin.php";
}

function getModuleAdminFilePath2($module): string {
	return getModulePath($module, true) . "admin.php";
}

function getModuleMainFilePath($module): string {
	return getModulePath($module, true) . $module . "_main.php";
}

function getModuleMainFilePath2($module): string {
	return getModulePath($module, true) . "main.php";
}

function getModuleUninstallScriptPath(
		string $module,
		bool $abspath = true
): string {
	return getModulePath($module, $abspath) . $module . "_uninstall.php";
}

function getModuleUninstallScriptPath2(
		string $module,
		bool $abspath = true
): string {
	return getModulePath($module, $abspath) . "uninstall.php";
}

/**
 * Output buffer flusher
 * Forces a flush of the output buffer to screen useful
 * for displaying long loading lists eg: bulk emailers on screen
 * Stops the end user seeing loads of just plain old white
 * and thinking the browser has crashed on long loading pages.
 */
function fcflush(): void {
	static $output_handler = null;
	if ($output_handler === null) {
		$output_handler = @ini_get('output_handler');
	}
	if ($output_handler == 'ob_gzhandler') {
// forcing a flush with this is very bad
		return;
	}
	flush();
	if (function_exists('ob_flush') and function_exists('ob_get_length')
			and ob_get_length() !== false) {
		ob_flush();
	} else if (function_exists('ob_end_flush') and function_exists('ob_start')
			and function_exists('ob_get_length') and ob_get_length() !== FALSE) {
		@ob_end_flush();
		@ob_start();
	}
}

function no_cache($do = false): void {
	if ($do) {
		Flags::setNoCache(true);
	} else if (get_cache_control() == "auto"
			or get_cache_control() == "no_cache") {
		Flags::setNoCache(true);
	}
}

function isModuleInstalled(string $name): bool {
	$module = new Module($name);
	return $module->isInstalled();
}

function getAllModules(): array {
	if (Vars::get("allModules")) {
		return Vars::get("allModules");
	}
	$pkg = new PackageManager();
	$modules = $pkg->getInstalledPackages('modules');
	Vars::set("allModules", $modules);
	return $modules;
}

function stringContainsShortCodes(string $content, ?string $module = null): bool {
	$quot = '(' . preg_quote('&quot;') . ')?';
	return boolval(
			$module ?
			preg_match('/\[module=\"?' . $quot . preg_quote($module) . '\"?' .
					$quot . '\]/m', $content) :
			preg_match('/\[module=\"?' . $quot . '([a-zA-Z0-9_]+)\"?' .
					$quot . '\]/m', $content)
	);
}

// replace Shortcodes with modules
function replaceShortcodesWithModules(
		string $string,
		bool $replaceOther = true
): string {
	$string = $replaceOther ? replaceOtherShortCodes($string) : $string;

	$allModules = ModuleHelper::getAllEmbedModules();
	$disabledModules = Vars::get("disabledModules");

	foreach ($allModules as $module) {
		if (faster_in_array($module, $disabledModules)
				or ! stringContainsShortCodes($string, $module)) {
			continue;
		}
		$stringToReplace1 = '[module="' . $module . '"]';
		$stringToReplace2 = '[module=&quot;' . $module . '&quot;]';
		$stringToReplace3 = '[module=' . $module . ']';

		$module_mainfile_path = getModuleMainFilePath($module);
		$module_mainfile_path2 = getModuleMainFilePath2($module);

		if (file_exists($module_mainfile_path)) {
			require_once $module_mainfile_path;
		} else if (file_exists($module_mainfile_path2)) {
			require_once $module_mainfile_path2;
		}

		$main_class = getModuleMeta($module, "main_class");
		$controller = null;
		if ($main_class) {
			$controller = ControllerRegistry::get($main_class);
		}
		if ($controller and method_exists($controller, "render")) {
			$html_output = $controller->render();
		} else if (function_exists($module . "_render")) {
			$html_output = call_user_func($module . "_render");
		} else {
			throw new BadMethodCallException("Module $module "
					. "has no render() method");
		}

		$string = str_replace($stringToReplace1, $html_output, $string);
		$string = str_replace($stringToReplace2, $html_output, $string);

		$string = str_replace($stringToReplace3, $html_output, $string);
		$string = str_replace('[title]', get_title(), $string);
	}
	$string = replaceVideoTags($string);
	$string = replaceAudioTags($string);

	$string = optimizeHtml($string);
	return $string;
}

function replaceOtherShortCodes(string $string): string {
	$string = str_ireplace('[title]', get_title(), $string);
	ob_start();
	logo();
	$string = str_ireplace('[logo]', ob_get_clean(), $string);
	$language = getCurrentLanguage(true);
	$checkbox = new PrivacyCheckbox($language);
	$string = str_ireplace(
			"[accept_privacy_policy]",
			$checkbox->render(),
			$string
	);
	ob_start();
	site_slogan();
	$string = str_ireplace('[motto]', ob_get_clean(), $string);
	ob_start();
	site_slogan();
	$string = str_ireplace('[slogan]', ob_get_clean(), $string);

	$string = str_ireplace('[category]', get_category(), $string);

	$token = get_csrf_token_html() .
			'<input type="url" name="my_homepage_url" '
			. 'class="antispam_honeypot" value="" autocomplete="nope">';
	$string = str_ireplace('[csrf_token_html]', $token, $string);

// [tel] Links for tel Tags
	$string = preg_replace(
			'/\[tel\]([^\[\]]+)\[\/tel\]/i',
			'<a href="tel:$1" class="tel">$1</a>',
			$string
	);
	$string = preg_replace(
			'/\[skype\]([^\[\]]+)\[\/skype\]/i',
			'<a href="skye:$1?call" class="skype">$1</a>',
			$string
	);

	$string = str_ireplace("[year]", Template::getYear(), $string);
	$string = str_ireplace(
			"[homepage_owner]",
			Template::getHomepageOwner(),
			$string
	);

	preg_match_all("/\[include=([0-9]+)]/i", $string, $match);

	if (count($match) > 0) {
		for ($i = 0; $i < count($match[0]); $i ++) {
			$placeholder = $match[0][$i];
			$id = unhtmlspecialchars($match[1][$i]);
			$id = intval($id);

			$page = ContentFactory::getByID($id);
// a page should not include itself
// because that would cause an endless loop
			if ($page and $id != get_ID()) {
				$content = "";
				if ($page->active and checkAccess($page->access)) {
					$content = $page->content;
				}
				$string = str_ireplace($placeholder, $content, $string);
			}
		}
	}
	return $string;
}

function getPageByID(int $id): ?object {
	$id = intval($id);
	$result = db_query("SELECT * FROM " . tbname("content") .
			" where id = " . $id);
	if (db_num_rows($result) > 0) {
		return db_fetch_object($result);
	}
	return null;
}

// get page id by slug
function getPageIDBySlug(string $slug) {
	$result = db_query("SELECT slug, id FROM `" . tbname("content")
			. "` where slug='" . db_escape($slug) . "'");
	if (db_num_rows($result) > 0) {
		$row = db_fetch_object($result);
		return $row->id;
	}
	return null;
}

function getPageSlugByID(?int $id): ?string {
	$result = db_query("SELECT slug, id FROM `" . tbname("content")
			. "` where id=" . intval($id));
	if (db_num_rows($result) > 0) {
		$row = db_fetch_object($result);
		return $row->slug;
	}
	return null;
}

function getPageTitleByID(?int $id): string {
	$result = db_query("SELECT title, id FROM `" . tbname("content")
			. "` where id=" . intval($id));
	if (db_num_rows($result) > 0) {
		$row = db_fetch_object($result);
		return $row->title;
	}
	return "[" . get_translation("none") . "]";
}

// Get slugs of all pages
function getAllPagesWithTitle(): array {
	$result = db_query("SELECT slug, id, title FROM `" . tbname("content") .
			"` WHERE `deleted_at` IS NULL ORDER BY slug");
	$returnvalues = [];
	while ($row = db_fetch_object($result)) {
		$a = Array(
			$row->title,
			$row->slug . ".html"
		);
		array_push($returnvalues, $a);
		if (containsModule($row->slug, "blog")) {

			$sql = "select title, seo_shortname from " . tbname("blog")
					. " ORDER by datum DESC";
			$query_blog = db_query($sql);
			while ($row_blog = db_fetch_object($query_blog)) {
				$title = $row->title . " -> " . $row_blog->title;
				$url = $row->slug . ".html" . "?single=" .
						$row_blog->seo_shortname;
				$b = [
					$title,
					$url
				];
				array_push($returnvalues, $b);
			}
		}
	}
	return $returnvalues;
}

// Get all pages
function getAllPages(
		string $lang = null,
		string $order = "slug",
		bool $exclude_hash_links = true,
		string $menu = null
): array {
	if (!$lang) {
		if (!$menu) {
			$result = db_query("SELECT * FROM `" . tbname("content") .
					"` WHERE `deleted_at` IS NULL ORDER BY $order");
		} else {
			$result = db_query("SELECT * FROM `" . tbname("content") .
					"` WHERE `deleted_at` IS NULL and menu = '" .
					Database::escapeValue($menu) . "' ORDER BY $order");
		}
	} else {
		if (!$menu) {
			$result = db_query("SELECT * FROM `" . tbname("content") .
					"` WHERE `deleted_at` IS NULL AND language ='" .
					db_escape($lang) . "' ORDER BY $order");
		} else {
			$result = db_query("SELECT * FROM `" . tbname("content") .
					"` WHERE `deleted_at` IS NULL AND language ='" .
					db_escape($lang) . "' and menu = '" .
					Database::escapeValue($menu) . "' ORDER BY $order");
		}
	}
	$returnvalues = [];
	while ($row = db_fetch_assoc($result)) {
		if (!$exclude_hash_links or ( $exclude_hash_links
				and $row["type"] != "link" and $row["type"] != "node"
				and $row["type"] != "language_link")) {
			array_push($returnvalues, $row);
		}
	}

	return $returnvalues;
}

// Get slugs of all pages
function getAllSlugs(string $lang = null): array {
	$slugs = [];

	if (!$lang) {
		$result = db_query("SELECT slug,id FROM `" . tbname("content") .
				"` WHERE `deleted_at` IS NULL AND link_url "
				. "NOT LIKE '#%' ORDER BY slug");
	} else {

		$result = db_query("SELECT slug,id FROM `" . tbname("content") .
				"` WHERE `deleted_at` IS NULL  AND link_url "
				. "NOT LIKE '#%' AND language ='" . db_escape($lang) .
				"' ORDER BY slug");
	}
	while ($row = db_fetch_object($result)) {
		array_push($slugs, $row->slug);
	}

	return $slugs;
}

// Sprachcodes abfragen und als Array zurück geben
function getAllLanguages($filtered = false): array {
	$languageCodes = [];

	if ($filtered) {
		$permissionChecker = new PermissionChecker(get_user_id());
		$languages = $permissionChecker->getLanguages();
		if (count($languages) > 0) {
			$result = [];
			foreach ($languages as $lang) {
				$result[] = $lang->getLanguageCode();
			}
			return $result;
		}
	}
	if (!is_null(Vars::get("all_languages"))) {
		return Vars::get("all_languages");
	}
	$result = db_query("SELECT language_code FROM `" . tbname("languages") .
			"` ORDER BY language_code");

	while ($row = db_fetch_object($result)) {
		array_push($languageCodes, $row->language_code);
	}
	Vars::set("all_languages", $languageCodes);
	return $languageCodes;
}

// Gibt die Identifier aller Menüs zurück.
// Zusätzliche Navigationsmenüs können definiert werden,
// durch setzen von additional_menus
function getAllMenus(bool $only_used = false, $read_theme_menus = true): array {
	$menus = Array(
		"left",
		"top",
		"right",
		"bottom",
		"not_in_menu"
	);
	$additional_menus = Settings::get("additional_menus");

	if ($additional_menus) {
		$additional_menus = explode(";", $additional_menus);
		foreach ($additional_menus as $m) {
			array_push($menus, $m);
		}
	}
	if ($only_used) {
		$used = get_all_used_menus();
		$new_menus = [];
		for ($i = 0; $i <= count($menus); $i ++) {
			if (faster_in_array($menus[$i], $used)) {
				$new_menus[] = $menus[$i];
			}
		}
		$menus = $new_menus;
	}

	$themesList = getAllThemes();
	$allThemeMenus = [];
	foreach ($themesList as $theme) {
		$themeMenus = getThemeMeta($theme, "menus");
		if ($themeMenus and is_array($themeMenus)) {
			foreach ($themeMenus as $m) {
				if (!faster_in_array($m, $allThemeMenus)) {
					$allThemeMenus[] = $m;
				}
			}
		}
	}

	if ($read_theme_menus and count($allThemeMenus) > 0) {
		$menus = $allThemeMenus;
	}

	if (!faster_in_array("not_in_menu", $menus)) {
		$menus[] = "not_in_menu";
	}

	sort($menus);
	return $menus;
}

// Check if site contains a module
function containsModule(?string $page = null, ?string $module = null): bool {
	if (is_null($page)) {
		$page = get_requested_pagename();
	}

	if (!is_null(Vars::get("page_" . $page . "_contains_" . $module))) {
		return Vars::get("page_" . $page . "_contains_" . $module);
	}

	$result = db_query("SELECT content, module, `type` FROM " .
			tbname("content") . " WHERE slug = '" . db_escape($page) . "'");
	$dataset = db_fetch_assoc($result);
	$content = $dataset["content"];
	$content = str_replace("&quot;", "\"", $content);
	if (!is_null($dataset["module"]) and ! empty($dataset["module"])
			and $dataset["type"] == "module") {
		if (!$module or ( $module and $dataset["module"] == $module)) {
			Vars::set("page_" . $page . "_contains_" . $module, true);
			return true;
		}
	} else {
		$match = stringContainsShortCodes($content, $module);
		Vars::set("page_" . $page . "_contains_" . $module, $match);
		return $match;
	}
	Vars::set("page_" . $page . "_contains_" . $module, false);
	return false;
}

// API-Aufruf zur Deinstallation eines Moduls
// Ruft uninstall Script auf, falls vorhanden
// Löscht anschließend den Ordner modules/$name
// TODO: dies in die PackageManager Klasse verschieben
function uninstall_module(string $name, string $type = "module") {
	$acl = new ACL();
	if (!$acl->hasPermission("install_packages") and ! isCLI()) {
		return false;
	}

	$name = trim(basename(trim($name)));

	// Verhindern, dass der Modulordner oder gar das ganze
	// CMS gelöscht werden kann
	if ($name == "." or $name == ".." or empty($name)) {
		return false;
	}
	switch ($type) {
		case "module":
			$moduleDir = getModulePath($name, true);

			// Modul-Ordner entfernen
			if (is_dir($moduleDir)) {
				$uninstall_script = getModuleUninstallScriptPath($name, true);
				$uninstall_script2 = getModuleUninstallScriptPath2($name, true);

// Uninstall Script ausführen, sofern vorhanden
				$mainController = ModuleHelper::getMainController($name);
				if ($mainController
						and method_exists($mainController, "uninstall")) {
					$mainController->uninstall();
				} else if (file_exists($uninstall_script)) {
					require $uninstall_script;
				} else if (file_exists($uninstall_script2)) {
					require $uninstall_script2;
				}
				sureRemoveDir($moduleDir, true);
				clearCache();
				return !is_dir($moduleDir);
			}
			break;
		case "theme":
			$cTheme = Settings::get("theme");
			$allThemes = getAllThemes();

			if (faster_in_array($name, $allThemes) and $cTheme !== $name) {
				$theme_path = getTemplateDirPath($name, true);
				sureRemoveDir($theme_path, true);
				clearCache();
				return !is_dir($theme_path);
			}
			break;
	}

	return false;
}

// returns version number of UliCMS Core
function cms_version(): string {
	$v = new UliCMSVersion();
	return implode(".", $v->getInternalVersion());
}

function get_environment(): string {
	return getenv("ULICMS_ENVIRONMENT") ?
			getenv("ULICMS_ENVIRONMENT") : "default";
}

function func_enabled(string $func): array {
	$disabled = explode(',', ini_get('disable_functions'));
	foreach ($disabled as $disableFunction) {
		$is_disabled[] = trim($disableFunction);
	}
	if (faster_in_array($func, $is_disabled)) {
		$it_is_disabled["m"] = $func .
				'() has been disabled for security reasons in php.ini';
		$it_is_disabled["s"] = 0;
	} else {
		$it_is_disabled["m"] = $func . '() is allow to use';
		$it_is_disabled["s"] = 1;
	}
	return $it_is_disabled;
}
