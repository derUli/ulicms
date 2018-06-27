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
        
        if (! isset($_POST["log_ip"])) {
            Settings::delete("log_ip");
        } else {
            setconfig("log_ip", "log_ip");
        }
        
        if (! isset($_POST["delete_ips_after_48_hours"])) {
            Settings::delete("delete_ips_after_48_hours");
        } else {
            setconfig("delete_ips_after_48_hours", "delete_ips_after_48_hours");
        }
        
        $varName = StringHelper::isNotNullOrWhitespace($language) ? "privacy_policy_checkbox_text_{$language}" : "privacy_policy_checkbox_text";
        Settings::set($varName, Request::getVar("privacy_policy_checkbox_text", ""));
        Response::redirect(ModuleHelper::buildActionURL("privacy_settings", "save=1&language={$language}"));
    }
}