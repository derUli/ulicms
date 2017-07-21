<?php
class Sitemap2 extends Controller {
	private $moduleName = "sitemap2";
	public function render() {
		return Template::executeModuleTemplate ( $this->moduleName, "sitemap.php" );
	}
	public function getShowNotInMenu() {
		return Settings::get ( "sitemap2_show_not_in_menu", "bool" );
	}
	public function settings() {
		return Template::executeModuleTemplate ( $this->moduleName, "settings.php" );
	}
}