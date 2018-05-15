<?php
use UliCMS\HTML\Input as Input;

class PrivacyCheckbox
{

    private $language;

    const CHECKBOX_NAME = "accept_privacy_policy";

    public function __construct($language)
    {
        $this->language = $language;
    }

    public function isEnabled()
    {
        return boolval(Settings::get("privacy_policy_checkbox_enable_{$this->language}", "bool"));
    }

    public function getCheckboxName()
    {
        return self::CHECKBOX_NAME;
    }

    Public function isChecked()
    {
        return StringHelper::isNotNullOrWhitespace(Request::getVar($this->getCheckboxName(), "", "str"));
    }

    public function check($success = null, $failed = null)
    {
        if ($this->isChecked()) {
            if ($success != null) {
                $success();
            }
        } else {
            if ($fail != null) {
                $fail();
            } else {
                ViewBag::set("exception", get_translation("please_accept_privacy_conditions"));
                echo Template::executeDefaultOrOwnTemplate("exception.php");
            }
        }
    }

    public function render()
    {
        $checkboxHtml = Input::CheckBox($this->getCheckboxName(), false, "✔", array(
            "required" => "required",
            "id" => $this->getCheckboxName()
        ));
        $fullHtml = Settings::get("privacy_policy_checkbox_text_{$this->language}");
        if (! $this->isEnabled() || StringHelper::isNullOrWhitespace($fullHtml)) {
            return "";
        }
        $fullHtml = str_ireplace("[checkbox]", $checkboxHtml, $fullHtml);
        return $fullHtml;
    }
}