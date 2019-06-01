<?php

require_once "init.php";

use UliCMS\Models\Content\Language;
use UliCMS\Utils\CacheUtil;
use UliCMS\Creators\CSVCreator;
use UliCMS\Creators\JSONCreator;
use UliCMS\Creators\PDFCreator;
use UliCMS\Creators\PlainTextCreator;

global $connection;

do_event("before_session_start");

// initialize session
@session_start();
// $_COOKIE[session_name()] = session_id();

do_event("after_session_start");

setLanguageByDomain();

$languages = getAllLanguages();

if (!empty($_GET["language"]) and faster_in_array($_GET["language"], $languages)) {
    $_SESSION["language"] = Database::escapeValue($_GET["language"], DB_TYPE_STRING);
}

if (!isset($_SESSION["language"])) {
    $_SESSION["language"] = Settings::get("default_language");
}

setLocaleByLanguage();

if (faster_in_array($_SESSION["language"], $languages) && is_file(getLanguageFilePath($_SESSION["language"]))) {
    require_once getLanguageFilePath($_SESSION["language"]);
} else if (is_file(getLanguageFilePath("en"))) {
    require getLanguageFilePath("en");
}

Translation::loadAllModuleLanguageFiles($_SESSION["language"]);
Translation::includeCustomLangFile($_SESSION["language"]);

Translation::loadCurrentThemeLanguageFiles($_SESSION["language"]);
do_event("custom_lang_" . $_SESSION["language"]);

if ($_SERVER["REQUEST_METHOD"] == "POST" and ! defined("NO_ANTI_CSRF")) {
    if (!check_csrf_token()) {
        die("This is probably a CSRF attack!");
    }
    if (Settings::get("min_time_to_fill_form", "int") > 0) {
        check_form_timestamp();
    }
}

// call domain.de/?run_cron=1 with curl or a similiar tool
// to automatically execute cronjobs
if (Request::getVar("run_cron")) {
    do_event("before_cron");
    require 'lib/cron.php';
    do_event("after_cron");
    TextResult("finished cron at " . strftime("%x %X"), HttpStatusCode::OK);
}

$status = check_status();

if (Settings::get("redirection") != "" && Settings::get("redirection") != false) {
    do_event("before_global_redirection");
    header("Location: " . Settings::get("redirection"));
    exit();
}

$theme = get_theme();

if (isMaintenanceMode()) {
    do_event("before_maintenance_message");
    // Sende HTTP Status 503 und Retry-After im Wartungsmodus
    header($_SERVER["SERVER_PROTOCOL"] . " 503 Service Temporarily Unavailable");
    header('Status: 503 Service Temporarily Unavailable');
    header('Retry-After: 60');
    header("Content-Type: text/html; charset=utf-8");
    if (is_file(getTemplateDirPath($theme) . "maintenance.php")) {
        require_once getTemplateDirPath($theme) . "maintenance.php";
    } else {
        die(get_translation("UNDER_MAINTENANCE"));
    }
    do_event("after_maintenance_message");
    die();
}

if (isset($_GET["format"]) and ! empty($_GET["format"])) {
    $format = trim($_GET["format"]);
} else {
    $format = "html";
}

do_event("before_http_header");

$redirection = get_redirection();

if ($redirection and ( is_active() or is_logged_in())) {
    Request::redirect($redirection, 302);
}
try {
    $page = ContentFactory::getByID(get_ID());
    if (!is_null($page->id) and $page instanceof Language_Link) {
        $language = new Language($page->link_to_language);
        if (!is_null($language->getID()) and StringHelper::isNotNullOrWhitespace($language->getLanguageLink())) {
            Request::redirect($language->getLanguageLink());
        }
    }
} catch (Exception $e) {

}

if (isset($_GET["goid"])) {
    $goid = intval($_GET["goid"]);
    $url = ModuleHelper::getFullPageURLByID($goid);
    if ($url) {
        Request::redirect($url, 301);
    } else {
        $url = getBaseFolderURL();
        Request::redirect($url, 301);
    }
}

ControllerRegistry::runMethods();

header($_SERVER["SERVER_PROTOCOL"] . " " . $status);

if ($format == "html") {
    header("Content-Type: text/html; charset=utf-8");
} else if ($format == "pdf") {
    $pdf = new PDFCreator();
    $pdf->output();
} else if ($format == "csv") {
    $csv = new CSVCreator();
    $csv->output();
} else if ($format == "json") {
    $json = new JSONCreator();
    $json->output();
} else if ($format == "txt") {
    $plain = new PlainTextCreator();
    $plain->output();
} else {
    $format = "html";
}

do_event("after_http_header");

if (count(getThemeList()) === 0) {
    throw new Exception("Keine Themes vorhanden!");
}

if (!is_dir(getTemplateDirPath($theme, true))) {
    throw new Exception("The selected theme doesn't exists!");
}

do_event("before_functions");

if (is_file(getTemplateDirPath($theme, true) . "functions.php")) {
    require getTemplateDirPath($theme, true) . "functions.php";
}

do_event("after_functions");

$hasModul = containsModule(get_requested_pagename());

$cache_control = get_cache_control();
switch ($cache_control) {
    case "auto":
    case "force":
        Flags::setNoCache(false);
        break;
        break;
    case "no_cache":
        Flags::setNoCache(true);
        break;
}
if ($hasModul) {
    no_cache();
}

// Kein Caching wenn man eingeloggt ist
if (is_logged_in() and get_cache_control() == "auto") {
    no_cache();
}

do_event("before_html");

$cacheAdapter = null;
if (CacheUtil::isCacheEnabled() and Request::isGet() and ! Flags::getNoCache()) {
    $cacheAdapter = CacheUtil::getAdapter();
}
$uid = CacheUtil::getCurrentUid();
if ($cacheAdapter and $cacheAdapter->get($uid)) {
    echo $cacheAdapter->get($uid);

    if (Settings::get("no_auto_cron")) {
        die();
    }

    do_event("before_cron");
    @require 'lib/cron.php';
    do_event("after_cron");
    die();
}

if ($cacheAdapter or Settings::get("minify_html")) {
    ob_start();
}
$top_files = array(
    "type/" . get_type() . "/oben.php",
    "type/" . get_type() . "/top.php",
    "oben.php",
    "top.php"
);
foreach ($top_files as $file) {
    $file = getTemplateDirPath($theme, true) . $file;
    if (is_file($file)) {
        require $file;
        break;
    }
}
do_event("before_content");
$text_position = get_text_position();
if ($text_position == "after") {
    Template::outputContentElement();
}

$disable_functions = getThemeMeta(get_theme(), "disable_functions");

if (!(is_array($disable_functions) and faster_in_array("output_content", $disable_functions))) {
    content();
}

if ($text_position == "before") {
    Template::outputContentElement();
}

do_event("after_content");

do_event("before_edit_button");

if (!(is_array($disable_functions) and faster_in_array("edit_button", $disable_functions))) {
    edit_button();
}

do_event("after_edit_button");
$bottom_files = array(
    "type/" . get_type() . "/unten.php",
    "type/" . get_type() . "/bottom.php",
    "unten.php",
    "bottom.php"
);
foreach ($bottom_files as $file) {
    $file = getTemplateDirPath($theme, true) . $file;
    if (is_file($file)) {
        require $file;
        break;
    }
}

do_event("after_html");

if ($cacheAdapter or Settings::get("minify_html")) {
    $generatedHtml = ob_get_clean();
    $generatedHtml = normalizeLN($generatedHtml, "\n");
    $generatedHtml = optimizeHtml($generatedHtml);

    echo $generatedHtml;

    if ($cacheAdapter and ! defined("EXCEPTION_OCCURRED")) {
        $cacheAdapter->set($uid, $generatedHtml, CacheUtil::getCachePeriod());
    }
}

// Wenn no_auto_cron gesetzt ist, dann muss cron.php manuell ausgeführt bzw. aufgerufen werden
if (!Settings::get("no_auto_cron")) {
    do_event("before_cron");
    require 'lib/cron.php';
    do_event("after_cron");
}

exit();

