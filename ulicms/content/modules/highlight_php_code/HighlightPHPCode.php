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
	public function contentFilter($html) {
		preg_match_all ( "/\[code id=([0-9]+)]/i", $html, $match );
		if (count ( $match ) > 0) {
			for($i = 0; $i < count ( $match [0] ); $i ++) {
				$placeholder = $match [0] [$i];
				$id = intval ( unhtmlspecialchars ( $match [1] [$i] ) );
				$code = new PHPCode ( $id );
				$code = $code->getCode ();
				$codeHTML = '<div class="highlighted-php-code" id="highlighted-php-code-' . $id . '">';
				$codeHTML .= highlight_string ( $code, true );
				$codeHTML .= '</div>';
				$html = str_replace ( $placeholder, $codeHTML, $html );
			}
		}
		return $html;
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
	public function editCode() {
		if (Request::hasVar ( "name" ) and Request::hasVar ( "code" ) and Request::hasVar ( "name" )) {
			$ds = new PHPCode ( Request::getVar ( "id", null, "int" ) );
			if ($ds->getID ()) {
				$ds->setName ( Request::getVar ( "name" ) );
				$ds->setCode ( Request::getVar ( "code" ) );
				$ds->save ();
			}
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