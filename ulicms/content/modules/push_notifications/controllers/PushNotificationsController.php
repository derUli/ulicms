<?php
use UliCMS\Exceptions\NotImplementedException;
class PushNotificationsController extends MainClass {
	const MODULE_NAME = "push_notifications";
	public function afterInit() {
		Settings::register ( "engagespot/site_key", "" );
	}
	public function head() {
		// embed javascript from Engagespot.co
		return Template::executeModuleTemplate ( self::MODULE_NAME, "script.php" );
	}
	public function saveSettingsPost() {
		throw new NotImplementedException ();
	}
	public function settings() {
		// show settings page
		return Template::executeModuleTemplate ( self::MODULE_NAME, "admin.php" );
	}
	public function getSettingsHeadline() {
		return get_translation ( "push_notifications" );
	}
}