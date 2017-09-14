<?php
class SelectGoogleFontsController extends Controller {
	private $moduleName = "select_google_fonts";
	public function adminHead() {
		if (Request::getVar ( "action" ) == "module_settings" and Request::getVar ( "module" ) == $this->moduleName) {
			echo Template::executeModuleTemplate ( $this->moduleName, "head.php" );
		}
	}
	public function settings() {
		echo Template::executeModuleTemplate ( $this->moduleName, "settings.php" );
	}
	public function getSettingsHeadline() {
		return "Google Fonts";
	}
	public function save() {
		if (Request::getVar ( "google-font" )) {
			Settings::set ( "default-font", "google" );
			Settings::set ( "google-font", Request::getVar ( "google-font" ) );
		}
		Request::redirect ( ModuleHelper::buildAdminURL ( $this->moduleName, "save=1" ) );
	}
}
