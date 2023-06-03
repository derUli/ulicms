<?php

const CORE_COMPONENT = 'frontend';

require_once dirname(__FILE__) . '/init.php';

use App\Models\Content\Language;
use App\Storages\Vars;
use App\Translations\Translation;
use App\Utils\CacheUtil;

setLanguageByDomain();

$languages = getAllLanguages();

if (! empty($_GET['language']) && in_array($_GET['language'], $languages)) {
    $_SESSION['language'] = Database::escapeValue(
        $_GET['language'],
        DB_TYPE_STRING
    );
}

if (! isset($_SESSION['language'])) {
    $_SESSION['language'] = Settings::get('default_language');
}

if (in_array($_SESSION['language'], $languages) && is_file(getLanguageFilePath($_SESSION['language']))) {
    require_once getLanguageFilePath($_SESSION['language']);
} elseif (is_file(getLanguageFilePath('en'))) {
    require getLanguageFilePath('en');
}

Translation::loadAllModuleLanguageFiles($_SESSION['language']);
Translation::loadCurrentThemeLanguageFiles($_SESSION['language']);

do_event('custom_lang_' . $_SESSION['language']);

if (Request::isPost()) {
    if (! check_csrf_token()) {
        exit('Invalid CSRF token');
    }
    if (Settings::get('min_time_to_fill_form', 'int') > 0) {
        check_form_timestamp();
    }
}

// Call domain.de/?run_cron=1 with curl or a similiar tool to automatically execute cronjobs
if (Request::getVar('run_cron')) {
    do_event('cron');

    HTTPStatusCodeResult(\App\Constants\HttpStatusCode::NO_CONTENT);
}

$slug = strtolower($_GET['slug'] ?? '');
$slugParts = explode('.', $slug);

// No extension anymore since UliCMS 2023.1
// Redirect old urls to new one without extension
$formatExtensions = [
    'html',
    'pdf',
    'csv',
    'txt',
    'json',
    'rss'
];

$slugExtension = count($slugParts) > 1 ? end($slugParts) : null;

if (in_array($slugExtension, $formatExtensions)) {
    $newUrl = str_replace(".{$slugExtension}", '', Request::getRequestUri() ?? '');
    Response::redirect($newUrl, \App\Constants\HttpStatusCode::MOVED_PERMANENTLY);
}

$status = check_status();

if (Settings::get('redirection')) {
    do_event('before_global_redirection');
    send_header('Location: ' . Settings::get('redirection'));
    exit();
}

$theme = get_theme();

if (is_maintenance_mode()) {
    do_event('before_maintenance_message');
    // Sende HTTP Status 503 und Retry-After im Wartungsmodus
    send_header($_SERVER['SERVER_PROTOCOL'] .
            ' 503 Service Temporarily Unavailable');
    send_header('Status: 503 Service Temporarily Unavailable');
    send_header('Retry-After: 60');
    send_header('Content-Type: text/html; charset=utf-8');

    if (is_file(getTemplateDirPath($theme) . 'maintenance.php')) {
        require_once getTemplateDirPath($theme) . 'maintenance.php';
    } else {
        exit(get_translation('UNDER_MAINTENANCE'));
    }

    do_event('after_maintenance_message');
    exit();
}

setSCSSImportPaths([ULICMS_GENERATED_PRIVATE]);

do_event('before_http_header');

$redirection = get_redirection();

if ($redirection && (is_active() || is_logged_in())) {
    Response::redirect($redirection, 302);
}

if (get_ID()) {
    try {
        $page = ContentFactory::getByID(get_ID());
        if ($page->id !== null && $page instanceof Language_Link) {
            $language = new Language($page->link_to_language);
            if ($language->getID() !== null && ! empty(
                $language->getLanguageLink()
            )
            ) {
                Response::redirect($language->getLanguageLink());
            }
        }
    } catch (Exception $e) {
        // TODO: Log error
    }
}

if (isset($_GET['goid'])) {
    $goid = (int)$_GET['goid'];
    $url = \App\Helpers\ModuleHelper::getFullPageURLByID($goid);
    if ($url) {
        Response::redirect($url, 301);
    } else {
        $url = getBaseFolderURL();
        Response::redirect($url, 301);
    }
}

ControllerRegistry::runMethods();

send_header($_SERVER['SERVER_PROTOCOL'] . ' ' . $status);
send_header('Content-Type: text/html; charset=utf-8');

do_event('after_http_header');

if (count(getAllThemes()) === 0) {
    throw new Exception('Keine Themes vorhanden!');
}

if (! is_dir(getTemplateDirPath($theme, true))) {
    throw new Exception("The selected theme doesn't exists!");
}

do_event('before_functions');

if (is_file(getTemplateDirPath($theme, true) . 'functions.php')) {
    require getTemplateDirPath($theme, true) . 'functions.php';
}

do_event('after_functions');

$hasModule = containsModule(get_slug());

$cache_control = get_cache_control();

switch ($cache_control) {
    case 'auto':
    case 'force':
        Vars::setNoCache(false);
        break;
    case 'no_cache':
        Vars::setNoCache(true);
        break;
}

if ($hasModule) {
    Vars::setNoCache(false);
}

// Kein Caching wenn man eingeloggt ist
if (is_logged_in() && get_cache_control() === 'auto') {
    Vars::setNoCache(true);
}

do_event('before_html');

$cacheAdapter = null;
if (CacheUtil::isCacheEnabled() && Request::isGet() && ! Vars::getNoCache()) {
    $cacheAdapter = CacheUtil::getAdapter();
}

$uid = CacheUtil::getCurrentUid();

if ($cacheAdapter && $cacheAdapter->get($uid)) {
    if (! (bool)Settings::get('no_auto_cron')) {
        do_event('cron');
    }

    exit($cacheAdapter->get($uid));
}

if ($cacheAdapter || Settings::get('minify_html')) {
    ob_start();
}

$top_files = [
    'type/' . get_type() . '/oben.php',
    'type/' . get_type() . '/top.php',
    'oben.php',
    'top.php'
];

foreach ($top_files as $file) {
    $file = getTemplateDirPath($theme, true) . $file;
    if (is_file($file)) {
        require $file;
        break;
    }
}

do_event('before_content');
$text_position = get_text_position();
if ($text_position === 'after') {
    Template::outputContentElement();
}

$disable_functions = getThemeMeta(get_theme(), 'disable_functions');

if (! (is_array($disable_functions) && in_array('output_content', $disable_functions))) {
    content();
}

if ($text_position === 'before') {
    Template::outputContentElement();
}

do_event('after_content');
do_event('before_edit_button');

if (! (is_array($disable_functions) && in_array('edit_button', $disable_functions))) {
    Template::editButton();
}

do_event('after_edit_button');

$bottom_files = [
    'type/' . get_type() . '/unten.php',
    'type/' . get_type() . '/bottom.php',
    'unten.php',
    'bottom.php'
];
foreach ($bottom_files as $file) {
    $file = getTemplateDirPath($theme, true) . $file;
    if (is_file($file)) {
        require $file;
        break;
    }
}

do_event('after_html');

if ($cacheAdapter || Settings::get('minify_html')) {
    $generatedHtml = ob_get_clean();
    $generatedHtml = normalizeLN($generatedHtml, PHP_EOL);
    $generatedHtml = optimizeHtml($generatedHtml);

    echo $generatedHtml;

    if ($cacheAdapter && ! defined('EXCEPTION_OCCURRED')) {
        $cacheAdapter->set($uid, $generatedHtml, CacheUtil::getCachePeriod());
    }
}

// Wenn no_auto_cron gesetzt ist, dann muss cron
// manuell ausgeführt bzw. aufgerufen werden
if (! (bool)Settings::get('no_auto_cron')) {
    do_event('cron');
}

exit();
