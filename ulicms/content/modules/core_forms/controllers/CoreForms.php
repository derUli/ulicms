<?php

declare(strict_types=1);

class CoreForms extends Controller {

    protected function incSpamCount(): void{
        Settings::set(
                "contact_form_refused_spam_mails",
                Settings::get("contact_form_refused_spam_mails") + 1
        );
    }

    public function beforeHttpHeader(): void {
        if (StringHelper::isNotNullOrWhitespace(
                        Request::getVar("submit-cms-form")
                ) and Request::isPost()) {
            // apply spam filter if enabled
            if (Settings::get("spamfilter_enabled") == "yes") {
                // check if honeypot field is filled
                if (!empty($_POST["my_homepage_url"])) {
                    $this->incSpamCount();
                    HTMLResult(get_translation("honeypot_is_not_empty"), 403);
                }
                foreach ($_POST as $key => $value) {
                    if (Settings::get("disallow_chinese_chars")
                            and AntiSpamHelper::isChinese($_POST[$key])) {
                        $this->incSpamCount();
                        HTMLResult(get_translation("chinese_chars_not_allowed"), 403);
                    }
                    if (Settings::get("disallow_cyrillic_chars")
                            and AntiSpamHelper::isCyrillic($_POST[$key])) {
                        $this->incSpamCount();
                        HTMLResult(get_translation("cyrillic_chars_not_allowed"), 403);
                    }
                    if (Settings::get("disallow_rtl_chars")
                            and AntiSpamHelper::isRtl($_POST[$key])) {
                        $this->incSpamCount();
                        HTMLResult(get_translation("rtl_chars_not_allowed"), 403);
                    }

                    $badwordsCheck = AntiSpamHelper::containsBadwords(
                                    $_POST[$key]
                    );
                    if ($badwordsCheck) {
                        $this->incSpamCount();
                        HTMLResult(get_translation(
                                        "request_contains_badword",
                                        [
                                            "%word%" => $badwordsCheck
                                        ]
                                ), 403);
                    }
                }

                if (AntiSpamHelper::isCountryBlocked()) {
                    $this->incSpamCount();
                    $hostname = @gethostbyaddr(get_ip());
                    HTMLResult(get_translation("your_country_is_blocked", [
                        "%hostname%" => $hostname
                            ]), 403);
                }
                if (Settings::get("reject_requests_from_bots")
                        and AntiSpamHelper::checkForBot(get_useragent())) {
                    $this->incSpamCount();
                    HTMLResult(get_translation("bots_are_not_allowed", [
                        "%hostname%" => $hostname
                            ]), 403);
                }
            }
            $form_id = Request::getVar("submit-cms-form", null, "int");
            Forms::submitForm($form_id);
        }
    }

}
