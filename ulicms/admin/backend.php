<?php
// required because my local XAMPP is sometimes in wrong folder, so includes will fail
chdir(dirname(__FILE__));

require_once "../init.php";
@session_start();
$acl = new acl();

if ($acl->hasPermission($_REQUEST["type"]) and ($_REQUEST["type"] == "images" or $_REQUEST["type"] == "files" or $_REQUEST["type"] == "flash")) {
    $_CONFIG["disabled"] = false;
    $_SESSION['KCFINDER'] = array();
    $_SESSION['KCFINDER']['disabled'] = false;
}

// $_COOKIE[session_name()] = session_id();

do_event("after_session_start");

do_event("before_set_language_by_domain");
setLanguageByDomain();
do_event("after_set_language_by_domain");

$syslang = getSystemLanguage();
if (is_file(getLanguageFilePath($syslang))) {
    include_once getLanguageFilePath($syslang);
} else if (is_file(getLanguageFilePath("en"))) {
    include_once getLanguageFilePath("en");
}
Translation::loadAllModuleLanguageFiles($syslang);
do_event("before_include_custom_lang_file");
Translation::includeCustomLangFile($syslang);
do_event("after_include_custom_lang_file");
do_event("before_custom_lang");
do_event("custom_lang_" . $syslang);

do_event("after_custom_lang");

if (logged_in() and $_SERVER["REQUEST_METHOD"] == "POST" and ! isset($_REQUEST["ajax_cmd"]) and ! defined("NO_ANTI_CSRF")) {
    if (! check_csrf_token()) {
        die("This is probably a CSRF attack!");
    }
}

do_event("before_set_locale_by_language");
setLocaleByLanguage();
do_event("after_set_locale_by_language");

require_once "../templating.php";

$cfg = new CMSConfig();
if (isset($cfg->ip_whitelist) and is_array($cfg->ip_whitelist) and count($cfg->ip_whitelist) > 0 and ! faster_in_array(get_ip(), $cfg->ip_whitelist)) {
    translate("login_from_ip_not_allowed");
    die();
}
require_once "inc/queries.php";
@include_once "inc/sort_direction.php";

require_once "inc/logincheck.php";

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

do_event("before_ajax_handler");

if (isset($_REQUEST["ajax_cmd"])) {
    include_once "inc/ajax_handler.php";
    exit();
}
do_event("after_ajax_handler");

do_event("before_backend_run_methods");
ControllerRegistry::runMethods();
do_event("after_backend_run_methods");

include "inc/ulicms_head.php";

if (! $eingeloggt) {
    if (isset($_GET["register"])) {
        do_event("before_register_form");
        require_once "inc/registerform.php";
        do_event("after_register_form");
    } else if (isset($_GET["reset_password"])) {
        do_event("before_reset_password_form");
        require_once "inc/reset_password.php";
        do_event("before_after_password_form");
    } else {
        do_event("before_login_form");
        require_once "inc/loginform.php";
        do_event("after_login_form");
    }
} else {
    require_once "inc/adminmenu.php";
    global $actions;
    $actions = array();
    
    ActionRegistry::loadModuleActions();
    
    do_event("register_actions");
    
    if ($_SESSION["require_password_change"]) {
        require_once "inc/change_password.php";
    } else if (isset($actions[get_action()])) {
        $requiredPermission = ActionRegistry::getActionpermission(get_action());
        if ((! $requiredPermission) or ($requiredPermission and $acl->hasPermission($requiredPermission))) {
            include_once $actions[get_action()];
        } else {
            noPerms();
        }
    } else {
        translate("action_not_found");
    }
}

do_event("admin_footer");

require_once "inc/footer.php";

do_event("before_admin_cron");
require_once "inc/cron.php";
do_event("after_admin_cron");

db_close($connection);
exit();
?>
