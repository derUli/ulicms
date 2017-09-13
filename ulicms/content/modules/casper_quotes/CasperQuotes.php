<?php
class CasperQuotes extends Controller {
	private $moduleName = "casper_quotes";
	public function getRandomQuote() {
		$files = glob ( ModuleHelper::buildModuleRessourcePath ( $this->moduleName, "quotes/*.txt" ) );
		if (count ( $files ) > 0) {
			$randomFile = $files [array_rand ( $files, 1 )];
			$text = trim ( file_get_contents ( $randomFile ) );
			return $text;
		}
		return null;
	}
	public function render() {
		return Template::getEscape ( $this->getRandomQuote () );
	}
	public function accordionLayout() {
		$acl = new ACL ();
		if ($acl->hasPermission ( "casper_quotes" )) {
			return Template::executeModuleTemplate ( $this->moduleName, "dashboard.php" );
		}
		return "";
	}
}