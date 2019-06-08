<?php

// required because my local XAMPP is sometimes in wrong folder, so includes will fail

require_once "../init.php";

use UliCMS\Backend\BackendPageRenderer;

@session_start();

// $_COOKIE[session_name()] = session_id();

do_event("after_session_start");

do_event("before_set_language_by_domain");
setLanguageByDomain();
do_event("after_set_language_by_domain");

$syslang = getSystemLanguage();
if (file_exists(getLanguageFilePath($syslang))) {
    require_once getLanguageFilePath($syslang);
} else if (file_exists(getLanguageFilePath("en"))) {
    require_once getLanguageFilePath("en");
}
Translation::loadAllModuleLanguageFiles($syslang);

do_event("before_include_custom_lang_file");
Translation::includeCustomLangFile($syslang);

do_event("after_include_custom_lang_file");
do_event("before_custom_lang");
do_event("custom_lang_" . $syslang);

do_event("after_custom_lang");

// Cross-Site-Request-Forgery Protection
if (logged_in() and $_SERVER["REQUEST_METHOD"] == "POST" and ! isset($_REQUEST["ajax_cmd"]) and ! defined("NO_ANTI_CSRF")) {
    if (!check_csrf_token()) {
        ExceptionResult("This is probably a CSRF attack!", HttpStatusCode::FORBIDDEN);
    }
}

do_event("before_set_locale_by_language");
setLocaleByLanguage();
do_event("after_set_locale_by_language");

$cfg = new CMSConfig();
if (isset($cfg->ip_whitelist) and is_array($cfg->ip_whitelist) and count($cfg->ip_whitelist) > 0 and ! faster_in_array(get_ip(), $cfg->ip_whitelist)) {
    ExceptionResult(get_translation("login_from_ip_not_allowed"));
    die();
}

if (is_logged_in()) {
    db_query("UPDATE " . tbname("users") . " SET last_action = " . time() . " WHERE id = " . get_user_id());
}

header("Content-Type: text/html; charset=UTF-8");

// Ajax Handlers are deprcated since 2019.3 and will get removed in 2019.4
do_event("before_ajax_handler");
do_event("after_ajax_handler");

do_event("before_backend_run_methods");
ControllerRegistry::runMethods();
do_event("after_backend_run_methods");

$renderer = new BackendPageRenderer(BackendHelper::getAction());
$renderer->render();

