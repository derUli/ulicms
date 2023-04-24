<?php

const CORE_COMPONENT = 'admin';
require_once '../init.php';

use App\Backend\BackendPageRenderer;
use App\Translations\Translation;

App\Utils\Session\sessionStart();

do_event('after_session_start');

do_event('before_set_language_by_domain');
setLanguageByDomain();
do_event('after_set_language_by_domain');

// load the language files for the current language
// if there is no translation for the current language code
// then do a fallback to english locale
$syslang = getSystemLanguage();
if (is_file(getLanguageFilePath($syslang))) {
    require getLanguageFilePath($syslang);
} elseif (is_file(getLanguageFilePath('en'))) {
    require getLanguageFilePath('en');
}

Translation::loadAllModuleLanguageFiles($syslang);

// Cross-Site-Request-Forgery Protection
if (is_logged_in() && Request::isPost() && ! check_csrf_token()) {
    ExceptionResult('Invalid CSRF Token', HttpStatusCode::FORBIDDEN);
}

// set locale for date formats and other stuff
do_event('before_set_locale_by_language');
setLocaleByLanguage();
do_event('after_set_locale_by_language');

// If is logged in update last action
if (is_logged_in()) {
    $user = new User(get_user_id());
    $user->setLastAction(time());
}

send_header('Content-Type: text/html; charset=UTF-8');

// run controller methods if called
do_event('before_backend_run_methods');
ControllerRegistry::runMethods();
do_event('after_backend_run_methods');

// render backend page
$renderer = new BackendPageRenderer(BackendHelper::getAction());
$renderer->render();
