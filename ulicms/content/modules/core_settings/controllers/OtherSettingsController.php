<?php

class OtherSettingsController extends Controller
{

    public function savePost()
    {       
        if (isset($_POST["email_mode"]))
            setconfig("email_mode", db_escape($_POST["email_mode"]));
        
        if (isset($_POST["domain_to_language"])) {
            $domain_to_language = $_POST["domain_to_language"];
            $domain_to_language = str_replace("\r\n", "\n", $domain_to_language);
            $domain_to_language = trim($domain_to_language);
            setconfig("domain_to_language", db_escape($domain_to_language));
        }
                
        if (isset($_POST["smtp_auth"])) {
            setconfig("smtp_auth", "auth");
        } else {
            Settings::delete("smtp_auth");
        }
        
        if (isset($_POST["smtp_encryption"])) {
            Settings::set("smtp_encryption", $_POST["smtp_encryption"]);
        }
        
        if (isset($_POST["smtp_no_verify_certificate"])) {
            Settings::set("smtp_no_verify_certificate", "smtp_no_verify_certificate");
        } else {
            Settings::delete("smtp_no_verify_certificate");
        }
        
        if (isset($_POST["show_meta_generator"])) {
            Settings::delete("hide_meta_generator");
        } else {
            setconfig("hide_meta_generator", "hide");
        }
        
        if (! isset($_POST["twofactor_authentication"])) {
            Settings::delete("twofactor_authentication");
        } else {
            setconfig("twofactor_authentication", "twofactor_authentication");
        }
        
        if (! isset($_POST["no_auto_cron"])) {
            Settings::delete("no_auto_cron");
        } else {
            setconfig("no_auto_cron", "no_auto_cron");
        }
        
        if (isset($_POST["smtp_host"])) {
            setconfig("smtp_host", db_escape($_POST["smtp_host"]));
        }
        
        if (isset($_POST["smtp_port"])) {
            setconfig("smtp_port", intval($_POST["smtp_port"]));
        }
        
        if (isset($_POST["force_password_change_every_x_days"])) {
            setconfig("force_password_change_every_x_days", intval($_POST["force_password_change_every_x_days"]));
        }
        
        if (isset($_POST["max_failed_logins_items"])) {
            setconfig("max_failed_logins_items", intval($_POST["max_failed_logins_items"]));
        }
        
        if (isset($_POST["smtp_user"])) {
            setconfig("smtp_user", db_escape($_POST["smtp_user"]));
        }
        
        if (isset($_POST["smtp_password"])) {
            setconfig("smtp_password", db_escape($_POST["smtp_password"]));
        }
        Request::redirect(ModuleHelper::buildActionURL("other_settings"));
    }
}