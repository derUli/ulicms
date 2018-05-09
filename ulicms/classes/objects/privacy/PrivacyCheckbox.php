<?php
use UliCMS\HTML\Input as Input;

class PrivacyCheckbox
{

    private $language;

    public function __construct($language)
    {
        $this->language = $language;
    }

    public function isEnabled()
    {
        return boolval(Settings::get("privacy_policy_checkbox_enable_{$this->language}", "bool"));
    }

    public function render()
    {
        $checkboxHtml = Input::CheckBox("privacy_policy_checkbox", false, "1", array(
            "required" => "required"
        ));
        $fullHtml = Settings::get("privacy_policy_checkbox_text_{$this->language}}");
        if (! $this->isEnabled || StringHelper::isNullOrWhitespace($fullHtml)) {
            return "";
        }
        $fullHtml = str_ireplace("[checkbox]", $checkboxHtml, $fullHtml);
        return $fullHtml;
    }
}