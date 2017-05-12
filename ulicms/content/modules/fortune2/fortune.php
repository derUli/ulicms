<?php
class Fortune extends Controller {
	private $moduleName = "fortune2";
	public function accordionLayout() {
		return Template::executeModuleTemplate ( $this->moduleName, "dashboard" );
	}
	public function render() {
		return Template::executeModuleTemplate ( $this->moduleName, "default" );
	}
	public function contentFilter($text) {
		return str_replace ( "[fortune]", $this->render (), $text );
	}
	public function getRandomFortune() {
		if (is_admin_dir ())
			$lang = getSystemLanguage ();
		else
			$lang = getCurrentLanguage ( true );
		$fortuneDir = getModulePath ( $this->moduleName ) . "cookies/" . $lang . "/";
		if (! is_dir ( $fortuneDir )) {
			$fortuneDir = getModulePath ( $this->moduleName ) . "cookies/en/";
		}
		$fortuneFiles = scandir ( $fortuneDir );
		do {
			$file = array_rand ( $fortuneFiles, 1 );
			$file = $fortuneFiles [$file];
			$file = $fortuneDir . $file;
		} while ( ! is_file ( $file ) );
		
		$fileContent = file_get_contents ( $file );
		$fileContent = trim ( $fileContent );
		$fileContent = utf8_encode ( $fileContent );
		$fileContent = str_replace ( "\r\n", "\n", $fileContent );
		$fortunes = explode ( "%\n", $fileContent );
		$text = array_rand ( $fortunes, 1 );
		$text = $fortunes [$text];
		return $text;
	}
}