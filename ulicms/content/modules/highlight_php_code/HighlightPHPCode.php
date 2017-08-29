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
	public function deleteCode() {
		if (Request::hasVar ( "id" )) {
			$code = new PHPCode ( Request::getVar ( "id", null, "int" ) );
			$code->delete ();
		}
		Request::redirect ( ModuleHelper::buildAdminURL ( $this->moduleName ) );
	}
}