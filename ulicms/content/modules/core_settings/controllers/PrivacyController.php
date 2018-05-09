<?php

class PrivacyController extends Controller
{

    public function savePost()
    {
        $language = basename(Request::getVar("language", null, "str"));
        $varName = StringHelper::isNotNullOrWhitespace($language) ? "privacy_policy_checkbox_enable_{$language}" : "privacy_policy_checkbox_enable";
        
        if (Request::getVar("privacy_policy_checkbox_enable", 0, "int")) {
            Settings::set($varName, 1);
        } else {
            Settings::delete($varName);
        }
        
        $varName = StringHelper::isNotNullOrWhitespace($language) ? "privacy_policy_checkbox_text_{$language}" : "privacy_policy_checkbox_text";
        Settings::set($varName, Request::getVar("privacy_policy_checkbox_text", ""));
        Response::redirect(ModuleHelper::buildActionURL("privacy_settings", "save=1&language={$language}"));
    }
}