<?php
class SimpleSettingsController extends Controller {
	private $moduleName = "core_settings";
	public function savePost() {
		add_hook ( "before_safe_simple_settings" );
		setconfig ( "homepage_owner", db_escape ( $_POST ["homepage_owner"] ) );
		setconfig ( "language", db_escape ( $_POST ["language"] ) );
		setconfig ( "visitors_can_register", intval ( isset ( $_POST ["visitors_can_register"] ) ) );
		setconfig ( "maintenance_mode", intval ( isset ( $_POST ["maintenance_mode"] ) ) );
		setconfig ( "email", db_escape ( $_POST ["email"] ) );
		setconfig ( "max_news", ( int ) $_POST ["max_news"] );
		setconfig ( "logo_disabled", db_escape ( $_POST ["logo_disabled"] ) );
		setconfig ( "timezone", db_escape ( $_POST ["timezone"] ) );
		setconfig ( "robots", db_escape ( $_POST ["robots"] ) );
		
		if (! isset ( $_POST ["disable_password_reset"] )) {
			setconfig ( "disable_password_reset", "disable_password_reset" );
		} else {
			Settings::delete ( "disable_password_reset" );
		}
		add_hook ( "after_safe_simple_settings" );
		Request::redirect ( ModuleHelper::buildActionURL ( "settings_simple" ) );
	}
	public function getTimezones() {
		return file ( ModuleHelper::buildModuleRessourcePath ( $this->moduleName, "data/timezones.txt" ) );
	}
}