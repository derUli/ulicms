<?php

declare(strict_types=1);

use App\Utils\CacheUtil;

class OtherSettingsController extends Controller {

    public function _savePost(): void {
        if (isset($_POST["email_mode"])) {
            Settings::set("email_mode", $_POST["email_mode"]);
        }

        if (isset($_POST["domain_to_language"])) {
            $domain_to_language = $_POST["domain_to_language"];
            $domain_to_language = str_replace("\r\n", "\n", $domain_to_language);
            $domain_to_language = trim($domain_to_language);
            Settings::set("domain_to_language", $domain_to_language);
        }

        if (isset($_POST["smtp_auth"])) {
            Settings::set("smtp_auth", "auth");
        } else {
            Settings::delete("smtp_auth");
        }

        if (isset($_POST["smtp_encryption"])) {
            Settings::set("smtp_encryption", $_POST["smtp_encryption"]);
        }

        if (isset($_POST["smtp_no_verify_certificate"])) {
            Settings::set(
                    "smtp_no_verify_certificate",
                    "smtp_no_verify_certificate"
            );
        } else {
            Settings::delete("smtp_no_verify_certificate");
        }

        if (!isset($_POST["twofactor_authentication"])) {
            Settings::delete("twofactor_authentication");
        } else {
            Settings::set(
                    "twofactor_authentication",
                    "twofactor_authentication"
            );
        }

        if (!isset($_POST["no_auto_cron"])) {
            Settings::delete("no_auto_cron");
        } else {
            Settings::set("no_auto_cron", "no_auto_cron");
        }

        if (isset($_POST["smtp_host"])) {
            Settings::set("smtp_host", $_POST["smtp_host"]);
        }

        if (isset($_POST["smtp_port"])) {
            Settings::set("smtp_port", intval($_POST["smtp_port"]));
        }

        if (isset($_POST["max_failed_logins_items"])) {
            Settings::set(
                    "max_failed_logins_items",
                    intval($_POST["max_failed_logins_items"])
            );
        }

        if (isset($_POST["smtp_user"])) {
            Settings::set("smtp_user", $_POST["smtp_user"]);
        }

        if (isset($_POST["smtp_password"])) {
            Settings::set("smtp_password", $_POST["smtp_password"]);
        }

        CacheUtil::clearPageCache();
    }

    public function savePost(): void {
        $this->_savePost();
        Response::sendHttpStatusCodeResultIfAjax(
                HttpStatusCode::OK,
                ModuleHelper::buildActionURL("other_settings")
        );
    }

}
