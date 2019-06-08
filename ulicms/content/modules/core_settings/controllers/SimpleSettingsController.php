<?php

use UliCMS\Utils\CacheUtil;

class SimpleSettingsController extends Controller {

    public function savePost() {
        do_event("before_safe_simple_settings");
        Settings::set("homepage_owner", $_POST["homepage_owner"]);
        Settings::set("language", $_POST["language"]);
        Settings::set("visitors_can_register", intval(isset($_POST["visitors_can_register"])));
        Settings::set("maintenance_mode", intval(isset($_POST["maintenance_mode"])));
        Settings::set("email", $_POST["email"]);
        Settings::set("max_news", (int) $_POST["max_news"]);
        Settings::set("timezone", $_POST["timezone"]);
        Settings::set("robots", $_POST["robots"]);

        if (!isset($_POST["disable_password_reset"])) {
            Settings::set("disable_password_reset", "disable_password_reset");
        } else {
            Settings::delete("disable_password_reset");
        }

        do_event("after_safe_simple_settings");

        CacheUtil::clearPageCache();

        // if called by ajax return no content to improve performance
        if (Request::isAjaxRequest()) {
            HTTPStatusCodeResult(HttpStatusCode::OK);
        }
        Request::redirect(ModuleHelper::buildActionURL("settings_simple"));
    }

    public function getTimezones() {
        return DateTimeZone::listIdentifiers(DateTimeZone::ALL);
    }

}
