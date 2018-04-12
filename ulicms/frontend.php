<?php
require_once "init.php";
global $connection;

add_hook("before_session_start");

// initialize session
@session_start();
$cfg = new CMSConfig();
$cookie_expires = (isset($cfg->cookie_expires) and $cfg->cookie_expires > 0) ? (time() + intval($cfg->cookie_expires)) : 0;
if (! isset($_COOKIE[session_name()])) {
    setcookie(session_name(), session_id(), $cookie_expires);
}


add_hook("after_session_start");

setLanguageByDomain();

$languages = getAllLanguages();

if (! empty($_GET["language"]) and faster_in_array($_GET["language"], $languages)) {
    $_SESSION["language"] = Database::escapeValue($_GET["language"], DB_TYPE_STRING);
}

if (! isset($_SESSION["language"])) {
    $_SESSION["language"] = Settings::get("default_language");
}

setLocaleByLanguage();

if (faster_in_array($_SESSION["language"], $languages) && file_exists(getLanguageFilePath($_SESSION["language"]))) {
    include_once getLanguageFilePath($_SESSION["language"]);
} else if (file_exists(getLanguageFilePath("en"))) {
    include getLanguageFilePath("en");
}

Translation::loadAllModuleLanguageFiles($_SESSION["language"]);
Translation::includeCustomLangFile($_SESSION["language"]);

require_once "templating.php";
Translation::loadCurrentThemeLanguageFiles($_SESSION["language"]);
add_hook("custom_lang_" . $_SESSION["language"]);

if ($_SERVER["REQUEST_METHOD"] == "POST" and ! defined("NO_ANTI_CSRF")) {
    if (! check_csrf_token()) {
        die("This is probably a CSRF attack!");
    }
    if (Settings::get("min_time_to_fill_form", "int") > 0) {
        check_form_timestamp();
    }
}

$status = check_status();

if (Settings::get("redirection") != "" && Settings::get("redirection") != false) {
    add_hook("before_global_redirection");
    header("Location: " . Settings::get("redirection"));
    exit();
}

$theme = get_theme();

if (isMaintenanceMode()) {
    add_hook("before_maintenance_message");
    // Sende HTTP Status 503 und Retry-After im Wartungsmodus
    header($_SERVER["SERVER_PROTOCOL"] . " 503 Service Temporarily Unavailable");
    header('Status: 503 Service Temporarily Unavailable');
    header('Retry-After: 60');
    header("Content-Type: text/html; charset=utf-8");
    if (file_exists(getTemplateDirPath($theme) . "maintenance.php")) {
        require_once getTemplateDirPath($theme) . "maintenance.php";
    } else {
        die(get_translation("UNDER_MAINTENANCE"));
    }
    add_hook("after_maintenance_message");
    die();
}

if (isset($_GET["format"]) and ! empty($_GET["format"])) {
    $format = trim($_GET["format"]);
} else {
    $format = "html";
}

add_hook("before_http_header");

$redirection = get_redirection();

if ($redirection and (is_active() or is_logged_in())) {
    Request::redirect($redirection, 302);
}
try {
    $page = ContentFactory::getByID(get_ID());
    if (! is_null($page->id) and $page instanceof Language_Link) {
        $language = new Language($page->link_to_language);
        if (! is_null($language->getID()) and StringHelper::isNotNullOrWhitespace($language->getLanguageLink())) {
            Request::redirect($language->getLanguageLink());
        }
    }
} catch (Exception $e) {}

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

add_hook("after_http_header");

if (count(getThemeList()) === 0) {
    throw new Exception("Keine Themes vorhanden!");
}

if (! is_dir(getTemplateDirPath($theme, true))) {
    throw new Exception("The selected theme doesn't exists!");
}

add_hook("before_functions");

if (file_exists(getTemplateDirPath($theme, true) . "functions.php")) {
    include getTemplateDirPath($theme, true) . "functions.php";
}

add_hook("after_functions");

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

add_hook("before_html");

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
    
    add_hook("before_cron");
    @include 'cron.php';
    add_hook("after_cron");
    die();
}

if ($cacheAdapter or Settings::get("minify_html")) {
    ob_start();
}

$html_file = page_has_html_file(get_requested_pagename());

if ($html_file) {
    if (file_exists($html_file)) {
        echo file_get_contents($html_file);
    } else {
        echo "File Not Found";
    }
} else {
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
    add_hook("before_content");
    $text_position = get_text_position();
    
    if ($text_position == "after") {
        Template::outputContentElement();
    }
    
    content();
    
    if ($text_position == "before") {
        Template::outputContentElement();
    }
    
    add_hook("after_content");
    
    add_hook("before_edit_button");
    
    edit_button();
    add_hook("after_edit_button");
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
}

add_hook("after_html");

// Wenn no_auto_cron gesetzt ist, dann muss cron.php manuell ausgeführt bzw. aufgerufen werden

if ($cacheAdapter or Settings::get("minify_html")) {
    $generatedHtml = ob_get_clean();
    $generatedHtml = normalizeLN($generatedHtml, "\n");
    if (Settings::get("minify_html")) {
        $generatedHtml = preg_replace('/^\h*\v+/m', '', $generatedHtml);
        
        $posBegin = strpos($generatedHtml, "<html");
        $posEnd = strpos($generatedHtml, "</head>");
        $posLength = $posEnd - $posBegin;
        $head = substr($generatedHtml, $posBegin, $posLength + strlen("</head>"));
        $head = str_replace("\n", "", $head);
        
        $generatedHtml = $head . substr($generatedHtml, $posEnd + strlen("</head>") + 1);
        $generatedHtml = str_replace("</body>\n</html>", "</body></html>", $generatedHtml);
    }
    echo $generatedHtml;
    
    if ($cacheAdapter and ! defined("EXCEPTION_OCCURRED")) {
        $cacheAdapter->set($uid, $generatedHtml, CacheUtil::getCachePeriod());
    }
}

if (Settings::get("no_auto_cron")) {
    die();
}
add_hook("before_cron");
@include 'cron.php';
add_hook("after_cron");
die();

