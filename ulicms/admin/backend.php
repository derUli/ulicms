<?php
// required because my local XAMPP is sometimes in wrong folder, so includes will fail
chdir(dirname(__FILE__));

require_once "../init.php";

@session_start();
$cfg = new CMSConfig();
$cookie_expires = (isset($cfg->cookie_expires) and $cfg->cookie_expires > 0) ? (time() + intval($cfg->cookie_expires)) : 0;
if (! isset($_COOKIE[session_name()])) {
    setcookie(session_name(), session_id(), $cookie_expires);
}

$acl = new acl();

if ($acl->hasPermission($_REQUEST["type"]) and ($_REQUEST["type"] == "images" or $_REQUEST["type"] == "files" or $_REQUEST["type"] == "flash")) {
    $_CONFIG["disabled"] = false;
    $_SESSION['KCFINDER'] = array();
    $_SESSION['KCFINDER']['disabled'] = false;
}

add_hook("after_session_start");

add_hook("before_set_language_by_domain");
setLanguageByDomain();
add_hook("after_set_language_by_domain");

$syslang = getSystemLanguage();
if (file_exists(getLanguageFilePath($syslang))) {
    include_once getLanguageFilePath($syslang);
} else if (file_exists(getLanguageFilePath("en"))) {
    include_once getLanguageFilePath("en");
}
Translation::loadAllModuleLanguageFiles($syslang);
add_hook("before_include_custom_lang_file");
Translation::includeCustomLangFile($syslang);
add_hook("after_include_custom_lang_file");
add_hook("before_custom_lang");
add_hook("custom_lang_" . $syslang);

add_hook("after_custom_lang");

if (logged_in() and $_SERVER["REQUEST_METHOD"] == "POST" and ! isset($_REQUEST["ajax_cmd"]) and ! defined("NO_ANTI_CSRF")) {
    if (! check_csrf_token()) {
        die("This is probably a CSRF attack!");
    }
}

add_hook("before_set_locale_by_language");
setLocaleByLanguage();
add_hook("after_set_locale_by_language");

require_once "../templating.php";

$cfg = new CMSConfig();
if (isset($cfg->ip_whitelist) and is_array($cfg->ip_whitelist) and count($cfg->ip_whitelist) > 0 and ! faster_in_array(get_ip(), $cfg->ip_whitelist)) {
    translate("login_from_ip_not_allowed");
    die();
}
require_once "inc/queries.php";
@include_once "inc/sort_direction.php";

require_once "inc/logincheck.php";

define("_SECURITY", true);

if ($_GET["action"] == "ulicms_news") {
    require_once "inc/ulicms_news.php";
    exit();
}

if (isset($_SESSION["ulicms_login"])) {
    $eingeloggt = $_SESSION["ulicms_login"];
    db_query("UPDATE " . tbname("users") . " SET last_action = " . time() . " WHERE id = " . $_SESSION["login_id"]);
} else {
    $eingeloggt = false;
}

header("Content-Type: text/html; charset=UTF-8");

add_hook("before_ajax_handler");

if (isset($_REQUEST["ajax_cmd"])) {
    include_once "inc/ajax_handler.php";
    exit();
}
add_hook("after_ajax_handler");

add_hook("before_backend_run_methods");
ControllerRegistry::runMethods();
add_hook("after_backend_run_methods");

add_hook("before_backend_header");
require_once "inc/header.php";
add_hook("after_backend_header");

if (! $eingeloggt) {
    if (isset($_GET["register"])) {
        add_hook("before_register_form");
        require_once "inc/registerform.php";
        add_hook("after_register_form");
    } else if (isset($_GET["reset_password"])) {
        add_hook("before_reset_password_form");
        require_once "inc/reset_password.php";
        add_hook("before_after_password_form");
    } else {
        add_hook("before_login_form");
        require_once "inc/loginform.php";
        add_hook("after_login_form");
    }
} else {
    require_once "inc/adminmenu.php";
    global $actions;
    $actions = array();
    
    ActionRegistry::loadModuleActions();
    
    add_hook("register_actions");
    
    if ($_SESSION["require_password_change"]) {
        require_once "inc/change_password.php";
    } else if (isset($actions[get_action()])) {
        $requiredPermission = ActionRegistry::getActionpermission(get_action());
        if ((! $requiredPermission) or ($requiredPermission and $acl->hasPermission($requiredPermission))) {
            include_once $actions[get_action()];
        } else {
            noperms();
        }
    } else {
        translate("action_not_found");
    }
}

add_hook("admin_footer");

require_once "inc/footer.php";

add_hook("before_admin_cron");
require_once "inc/cron.php";
add_hook("after_admin_cron");

db_close($connection);
exit();
?>
