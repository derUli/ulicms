<?php
class HighlightPHPCode extends Controller {
	private $moduleName = "highlight_php_code";
	public function getSettingsLinkText() {
		return get_translation ( "edit" );
	}
	public function getSettingsHeadline() {
		return "Highlighting PHP Code";
	}
	public function settings() {
		ViewBag::set ( "datasets", PHPCode::getAll () );
		return Template::executeModuleTemplate ( $this->moduleName, "list.php" );
	}
}