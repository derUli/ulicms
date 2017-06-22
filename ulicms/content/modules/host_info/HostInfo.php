<?php
class HostInfo extends Controller {
	private $moduleName = "host_info";
	public function getSettingsHeadline() {
		return get_translation ( "host_info" );
	}
	public function settings() {
		return Template::executeModuleTemplate ( $this->moduleName, "view.php" );
	}
}