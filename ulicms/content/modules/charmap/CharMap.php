<?php
class CharMap extends Controller {
	private $moduleName = "charmap";
	public function getSettingsHeadline() {
		return get_translation ( "charmap" );
	}
	public function getSettingsLinkText() {
		return get_translation ( "open" );
	}
	public function settings() {
		return Template::executeModuleTemplate ( $this->moduleName, "charmap.php" );
	}
	public function render() {
		return $this->settings ();
	}
}