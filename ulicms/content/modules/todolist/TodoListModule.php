<?php
class TodoListModule extends Controller {
	private $moduleName = "todolist";
	public function getSettingsLinkText() {
		return get_translation ( "open" );
	}
	public function getSettingsHeadline() {
		return get_translation ( "todolist" );
	}
	public function settings() {
		return Template::executeModuleTemplate ( $this->moduleName, "list.php" );
	}
}