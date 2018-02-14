<?php
class FooterHtml extends Controller {
	const MODULE_NAME = "footer_html";
	public function frontendFooter() {
		Settings::register ( "footer_html", "" );
		return trim(Settings::get ( "footer_html" ));
	}
	public function getSettingsHeadline() {
		return get_translation ( "footer_html" );
	}
	public function getSettingsLinkText() {
		return get_translation ( "edit" );
	}
	public function settings() {
		return Template::executeModuleTemplate ( self::MODULE_NAME, "settings.php" );
	}
	public function savePost() {
		if (isset ( $_POST ["footer_html"] )) {
			Settings::set ( "footer_html", $_POST ["footer_html"] );
		}
		Request::redirect ( ModuleHelper::buildAdminURL ( self::MODULE_NAME, "save=1" ) );
	}
}
