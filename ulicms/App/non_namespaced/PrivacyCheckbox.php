<?php

declare(strict_types=1);

use App\HTML\Input as Input;

// Class with DSGVO / GDPR related functions
class PrivacyCheckbox
{
    private $language;

    public const CHECKBOX_NAME = "accept_privacy_policy";

    public function __construct(string $language)
    {
        $this->language = $language;
    }

    // the gdpr checkbox must be enabled and configured by language
    public function isEnabled(): bool
    {
        return boolval(
            Settings::get(
                "privacy_policy_checkbox_enable_{$this->language}",
                "bool"
            )
        );
    }

    // returns the name of the checkbox input
    public function getCheckboxName(): string
    {
        return self::CHECKBOX_NAME;
    }

    // returns true if the checkbox is checked
    public function isChecked(): bool
    {
        $value = Request::getVar(
            $this->getCheckboxName(),
            "",
            "str"
        );
        return StringHelper::isNotNullOrWhitespace($value);
    }

    // check if the gdpr checkbox is checked
    // after that execute success or failed callback
    public function check(
        ?callable $success = null,
        ?callable $failed = null
    ): void {
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

    // render the dgpr checkbox input
    // the dgpr accept text can be written in a html editor
    // this method replaces the [checkbox] placeholder with the checkbox input
    public function render(): string
    {
        $checkboxHtml = Input::checkBox(
            $this->getCheckboxName(),
            false,
            "✔",
            [
                            "required" => "required",
                            "id" => $this->getCheckboxName()
                        ]
        );
        $fullHtml = Settings::get(
            "privacy_policy_checkbox_text_{$this->language}"
        );
        if (!$this->isEnabled() or
                StringHelper::isNullOrWhitespace($fullHtml)
        ) {
            return "";
        }
        $fullHtml = str_ireplace(
            "[checkbox]",
            $checkboxHtml,
            $fullHtml
        );
        return $fullHtml;
    }
}
