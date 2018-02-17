<?php
class OtherSettingsController extends Controller {
	public function savePost() {
		if (isset ( $_POST ["cache_period"] )) {
			setconfig ( "cache_period", intval ( $_POST ["cache_period"] ) * 60 );
		}
		
		if (isset ( $_POST ["email_mode"] ))
			setconfig ( "email_mode", db_escape ( $_POST ["email_mode"] ) );
		
		if (isset ( $_POST ["domain_to_language"] )) {
			$domain_to_language = $_POST ["domain_to_language"];
			$domain_to_language = str_replace ( "\r\n", "\n", $domain_to_language );
			$domain_to_language = trim ( $domain_to_language );
			setconfig ( "domain_to_language", db_escape ( $domain_to_language ) );
		}
		
		if (isset ( $_POST ["cache_enabled"] )) {
			Settings::delete ( "cache_disabled" );
		} else {
			setconfig ( "cache_disabled", "disabled" );
		}
		
		if (isset ( $_POST ["smtp_auth"] )) {
			setconfig ( "smtp_auth", "auth" );
		} else {
			Settings::delete ( "smtp_auth" );
		}
		
		if (isset ( $_POST ["show_meta_generator"] )) {
			Settings::delete ( "hide_meta_generator" );
		} else {
			setconfig ( "hide_meta_generator", "hide" );
		}
		
		if (! isset ( $_POST ["twofactor_authentication"] )) {
			Settings::delete ( "twofactor_authentication" );
		} else {
			setconfig ( "twofactor_authentication", "twofactor_authentication" );
		}
		
		if (! isset ( $_POST ["log_ip"] )) {
			Settings::delete ( "log_ip" );
		} else {
			setconfig ( "log_ip", "log_ip" );
		}
		
		if (! isset ( $_POST ["delete_ips_after_48_hours"] )) {
			Settings::delete ( "delete_ips_after_48_hours" );
		} else {
			setconfig ( "delete_ips_after_48_hours", "delete_ips_after_48_hours" );
		}
		
		if (! isset ( $_POST ["no_auto_cron"] )) {
			Settings::delete ( "no_auto_cron" );
		} else {
			setconfig ( "no_auto_cron", "no_auto_cron" );
		}
		
		if (isset ( $_POST ["smtp_host"] )) {
			setconfig ( "smtp_host", db_escape ( $_POST ["smtp_host"] ) );
		}
		
		if (isset ( $_POST ["smtp_port"] )) {
			setconfig ( "smtp_port", intval ( $_POST ["smtp_port"] ) );
		}
		
		if (isset ( $_POST ["force_password_change_every_x_days"] )) {
			setconfig ( "force_password_change_every_x_days", intval ( $_POST ["force_password_change_every_x_days"] ) );
		}
		
		if (isset ( $_POST ["max_failed_logins_items"] )) {
			setconfig ( "max_failed_logins_items", intval ( $_POST ["max_failed_logins_items"] ) );
		}
		
		if (isset ( $_POST ["smtp_user"] )) {
			setconfig ( "smtp_user", db_escape ( $_POST ["smtp_user"] ) );
		}
		
		if (isset ( $_POST ["smtp_password"] )) {
			setconfig ( "smtp_password", db_escape ( $_POST ["smtp_password"] ) );
		}
		Request::redirect ( ModuleHelper::buildActionURL ( "other_settings" ) );
	}
}