<?php
class WordpressXmlImporterHooks extends Controller {
	private $moduleName = "wordpress_xml_importer";
	public function getSettingsHeadline() {
		return "Wordpress XML Importer";
	}
	public function getSettingsText() {
		return get_translation ( "open" );
	}
	public function settings() {
		return Template::executeModuleTemplate ( $this->moduleName, "form.php" );
	}
}