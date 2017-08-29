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
	public function createCode() {
		if (Request::hasVar ( "name" ) and Request::hasVar ( "code" )) {
			$ds = new PHPCode ();
			$ds->setName ( Request::getVar ( "name" ) );
			$ds->setCode ( Request::getVar ( "code" ) );
			$ds->save ();
		}
		Request::redirect ( ModuleHelper::buildAdminURL ( $this->moduleName ) );
	}
	public function deleteCode() {
		if (Request::hasVar ( "id" )) {
			$code = new PHPCode ( Request::getVar ( "id", null, "int" ) );
			$code->delete ();
		}
		Request::redirect ( ModuleHelper::buildAdminURL ( $this->moduleName ) );
	}
}