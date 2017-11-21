<?php
class ModStarter extends Controller {
	const MODULE_NAME = "modstarter";
	const MODULE_TITLE = "Modstarter";
	public function settings() {
		return Template::executeModuleTemplate ( self::MODULE_NAME, "list.php" );
	}
	public function getSettingsHeadline() {
		return self::MODULE_TITLE;
	}
	public function getSettingsLinkText() {
		return get_translation ( "open" );
	}
}