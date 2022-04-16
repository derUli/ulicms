<?php

require_once "init.php";

use UliCMS\Models\Content\Language;
use UliCMS\Utils\CacheUtil;
use UliCMS\Renderers\CsvRenderer;
use UliCMS\Renderers\JsonRenderer;
use UliCMS\Renderers\PdfRenderer;
use UliCMS\Renderers\PlainTextRenderer;
use UliCMS\Storages\Flags;
use UliCMS\Utils\Session;
use UliCMS\Constants\HttpStatusCode;
use UliCMS\Localization\Translation;

global $connection;

do_event("before_session_start");

// initialize session
Session::sessionStart();

do_event("after_session_start");

setLanguageByDomain();

$languages = getAllLanguages();

if (!empty($_GET["language"]) && faster_in_array($_GET["language"], $languages)) {
    $_SESSION["language"] = Database::escapeValue(
                    $_GET["language"],
                    DB_TYPE_STRING
    );
}

if (!isset($_SESSION["language"])) {
    $_SESSION["language"] = Settings::get("default_language");
}

setLocaleByLanguage();

if (faster_in_array($_SESSION["language"], $languages) && file_exists(getLanguageFilePath($_SESSION["language"]))) {
    require_once getLanguageFilePath($_SESSION["language"]);
} elseif (file_exists(getLanguageFilePath("en"))) {
    require getLanguageFilePath("en");
}

Translation::loadAllModuleLanguageFiles($_SESSION["language"]);
Translation::includeCustomLangFile($_SESSION["language"]);

Translation::loadCurrentThemeLanguageFiles($_SESSION["language"]);
do_event("custom_lang_" . $_SESSION["language"]);

if (Request::isPost() && !defined("NO_ANTI_CSRF")) {
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
    TextResult("finished cron at " . PHP81_BC\strftime("%x %X"), HttpStatusCode::OK);
}

$status = check_status();

if (Settings::get("redirection")) {
    do_event("before_global_redirection");
    send_header("Location: " . Settings::get("redirection"));
    exit();
}

$theme = get_theme();

if (isMaintenanceMode()) {
    do_event("before_maintenance_message");
    // Sende HTTP Status 503 und Retry-After im Wartungsmodus
    send_header($_SERVER["SERVER_PROTOCOL"] .
            " 503 Service Temporarily Unavailable");
    send_header('Status: 503 Service Temporarily Unavailable');
    send_header('Retry-After: 60');
    send_header("Content-Type: text/html; charset=utf-8");
    if (file_exists(getTemplateDirPath($theme) . "maintenance.php")) {
        require_once getTemplateDirPath($theme) . "maintenance.php";
    } else {
        die(get_translation("UNDER_MAINTENANCE"));
    }
    do_event("after_maintenance_message");
    die();
}

if (isset($_GET["format"]) && !empty($_GET["format"])) {
    $format = trim($_GET["format"]);
} else {
    $format = "html";
}

setSCSSImportPaths([ULICMS_GENERATED]);

do_event("before_http_header");

$redirection = get_redirection();

if ($redirection && (is_active() or is_logged_in())) {
    Request::redirect($redirection, 302);
}
if (get_ID()) {
    try {
        $page = ContentFactory::getByID(get_ID());
        if (!is_null($page->id) && $page instanceof Language_Link) {
            $language = new Language($page->link_to_language);
            if (!is_null($language->getID()) && StringHelper::isNotNullOrWhitespace(
                            $language->getLanguageLink()
                    )
            ) {
                Request::redirect($language->getLanguageLink());
            }
        }
    } catch (Exception $e) {
        // TODO: Log error
    }
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

send_header($_SERVER["SERVER_PROTOCOL"] . " " . $status);

if ($format == "html") {
    send_header("Content-Type: text/html; charset=utf-8");
} elseif ($format == "pdf") {
    $pdf = new PdfRenderer();
    Result($pdf->render(), HttpStatusCode::OK, "application/pdf");
} elseif ($format == "csv") {
    $csv = new CsvRenderer();
    Result($csv->render(), HttpStatusCode::OK, "text/csv");
} elseif ($format == "json") {
    $json = new JsonRenderer();
    RawJSONResult($json->render());
} elseif ($format == "txt") {
    $plain = new PlainTextRenderer();
    TextResult($plain->render());
} else {
    ExceptionResult(
            get_secure_translation(
                    "unsupported_output_format",
                    [
                        "%format%" => $format
                    ]
            )
    );
}

do_event("after_http_header");

if (count(getAllThemes()) === 0) {
    throw new Exception("Keine Themes vorhanden!");
}

if (!is_dir(getTemplateDirPath($theme, true))) {
    throw new Exception("The selected theme doesn't exists!");
}

do_event("before_functions");

if (file_exists(getTemplateDirPath($theme, true) . "functions.php")) {
    require getTemplateDirPath($theme, true) . "functions.php";
}

do_event("after_functions");

$hasModul = containsModule(get_slug());

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
if (is_logged_in() && get_cache_control() == "auto") {
    no_cache();
}

do_event("before_html");

$cacheAdapter = null;
if (CacheUtil::isCacheEnabled() && Request::isGet() && !Flags::getNoCache()) {
    $cacheAdapter = CacheUtil::getAdapter();
}
$uid = CacheUtil::getCurrentUid();
if ($cacheAdapter && $cacheAdapter->get($uid)) {
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
    if (file_exists($file)) {
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

if (!(is_array($disable_functions) && faster_in_array("output_content", $disable_functions))) {
    content();
}

if ($text_position == "before") {
    Template::outputContentElement();
}

do_event("after_content");

do_event("before_edit_button");

if (!(is_array($disable_functions) && faster_in_array("edit_button", $disable_functions))) {
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
    if (file_exists($file)) {
        require $file;
        break;
    }
}

do_event("after_html");

if ($cacheAdapter || Settings::get("minify_html")) {
    $generatedHtml = ob_get_clean();
    $generatedHtml = normalizeLN($generatedHtml, "\n");
    $generatedHtml = optimizeHtml($generatedHtml);

    echo $generatedHtml;

    if ($cacheAdapter && !defined("EXCEPTION_OCCURRED")) {
        $cacheAdapter->set($uid, $generatedHtml, CacheUtil::getCachePeriod());
    }
}

// Wenn no_auto_cron gesetzt ist, dann muss cron.php
// manuell ausgeführt bzw. aufgerufen werden
if (!Settings::get("no_auto_cron")) {
    do_event("before_cron");
    require 'lib/cron.php';
    do_event("after_cron");
}

exit();
