<?php

declare(strict_types=1);

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use UliCMS\Constants\HttpStatusCode;

class SpamFilterController extends Controller {

    public function _savePost(): void {
        do_event("before_save_spamfilter_settings");

        if ($_POST["spamfilter_enabled"] == "yes") {
            Settings::set("spamfilter_enabled", "yes");
        } else {
            Settings::set("spamfilter_enabled", "no");
        }

        if (isset($_POST["country_blacklist"])) {
            Settings::set("country_blacklist", $_POST["country_blacklist"]);
        }

        if (isset($_POST["spamfilter_words_blacklist"])) {
            $blacklist = $_POST["spamfilter_words_blacklist"];
            Settings::set("spamfilter_words_blacklist", $blacklist);
        }

        if (isset($_POST["disallow_chinese_chars"])) {
            Settings::set("disallow_chinese_chars", "disallow");
        } else {
            Settings::delete("disallow_chinese_chars");
        }

        if (isset($_POST["disallow_cyrillic_chars"])) {
            Settings::set("disallow_cyrillic_chars", "disallow");
        } else {
            Settings::delete("disallow_cyrillic_chars");
        }

        if (isset($_POST["disallow_rtl_chars"])) {
            Settings::set("disallow_rtl_chars", "disallow");
        } else {
            Settings::delete("disallow_rtl_chars");
        }

        if (isset($_POST["reject_requests_from_bots"])) {
            Settings::set("reject_requests_from_bots", "1");
        } else {
            Settings::delete("reject_requests_from_bots");
        }

        if (isset($_POST["check_mx_of_mail_address"])) {
            Settings::set("check_mx_of_mail_address", "1");
        } else {
            Settings::delete("check_mx_of_mail_address");
        }

        $min_time_to_fill_form = Request::getVar(
                        "min_time_to_fill_form",
                        0,
                        "int"
        );

        Settings::set("min_time_to_fill_form", $min_time_to_fill_form);

        do_event("after_save_spamfilter_settings");
        // if called by ajax return no content to improve performance
    }

    public function savePost(): void {
        $this->_savePost();
        Response::sendHttpStatusCodeResultIfAjax(
                HttpStatusCode::OK,
                ModuleHelper::buildActionURL("spam_filter")
        );
    }

}
