<?php

declare(strict_types=1);

use UliCMS\HTML\Input as Input;

// Class with DSGVO / GDPR related functions
class PrivacyCheckbox {

    private $language;

    const CHECKBOX_NAME = "accept_privacy_policy";

    public function __construct(string $language) {
        $this->language = $language;
    }

    public function isEnabled(): bool {
        return boolval(Settings::get("privacy_policy_checkbox_enable_{$this->language}", "bool"));
    }

    public function getCheckboxName(): string {
        return self::CHECKBOX_NAME;
    }

    Public function isChecked(): bool {
        return StringHelper::isNotNullOrWhitespace(Request::getVar($this->getCheckboxName(), "", "str"));
    }

    public function check(?callable $success = null, ?callable $failed = null): void {
        if ($this->isChecked()) {
            if ($success != null) {
                $success();
            }
        } else {
            if ($failed != null) {
                $failed();
            } else {
                ViewBag::set("exception", get_translation("please_accept_privacy_conditions"));
                echo Template::executeDefaultOrOwnTemplate("exception.php");
                exit();
            }
        }
    }

    public function render(): string {
        $checkboxHtml = Input::checkBox($this->getCheckboxName(), false, "✔", array(
                    "required" => "required",
                    "id" => $this->getCheckboxName()
        ));
        $fullHtml = Settings::get("privacy_policy_checkbox_text_{$this->language}");
        if (!$this->isEnabled() || StringHelper::isNullOrWhitespace($fullHtml)) {
            return "";
        }
        $fullHtml = str_ireplace("[checkbox]", $checkboxHtml, $fullHtml);
        return $fullHtml;
    }

}
