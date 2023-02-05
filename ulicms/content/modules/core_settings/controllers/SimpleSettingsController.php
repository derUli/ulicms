<?php

declare(strict_types=1);

use UliCMS\Utils\CacheUtil;

class SimpleSettingsController extends Controller {

    public function _savePost(): void {
        do_event("before_safe_simple_settings");
        Settings::set("homepage_owner", $_POST["homepage_owner"]);
        Settings::set("language", $_POST["language"]);
        Settings::set(
                "visitors_can_register",
                intval(isset($_POST["visitors_can_register"]))
        );
        Settings::set(
                "maintenance_mode",
                intval(
                        isset($_POST["maintenance_mode"])
                )
        );
        Settings::set("email", $_POST["email"]);
        Settings::set("timezone", $_POST["timezone"]);
        Settings::set("robots", $_POST["robots"]);

        if (!isset($_POST["disable_password_reset"])) {
            Settings::set("disable_password_reset", "disable_password_reset");
        } else {
            Settings::delete("disable_password_reset");
        }

        do_event("after_safe_simple_settings");

        CacheUtil::clearPageCache();
    }

    public function savePost(): void {
        $this->_savePost();

        Response::sendHttpStatusCodeResultIfAjax(
                HttpStatusCode::OK,
                ModuleHelper::buildActionURL("settings_simple")
        );
    }

    public function getTimezones(): array {
        return DateTimeZone::listIdentifiers(DateTimeZone::ALL);
    }

}
