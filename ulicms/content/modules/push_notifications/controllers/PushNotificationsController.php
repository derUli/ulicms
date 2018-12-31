<?php
class PushNotificationsController extends MainClass {
	const MODULE_NAME = "push_notifications";
	const ENGAGESPOT_DASHBOARD_URL = "https://app.engagespot.co";
	public function afterInit() {
		Settings::register ( "engagespot/site_key", "" );
	}
	public function head() {
		// embed javascript from Engagespot.co
		return Template::executeModuleTemplate ( self::MODULE_NAME, "script.php" );
	}
	public function saveSettingsPost() {
		Settings::set ( "engagespot/site_key", Request::getVar ( "site_key", "", "str" ) );
		Request::redirect ( ModuleHelper::buildAdminURL ( self::MODULE_NAME ) );
	}
	public function settings() {
		// show settings page
		return Template::executeModuleTemplate ( self::MODULE_NAME, "admin.php" );
	}
	public function getSettingsHeadline() {
		return get_translation ( "push_notifications" );
	}
}