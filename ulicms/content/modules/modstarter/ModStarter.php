<?php
class ModStarter extends Controller {
	const MODULE_NAME = "modstarter";
	const MODULE_TITLE = "Modstarter";
	public function settings() {
		return Template::executeModuleTemplate ( self::MODULE_NAME, "list.php" );
	}
	public function getSettingsHeadline() {
		return self::MODULE_TITLE;
	}
	public function getSettingsLinkText() {
		return get_translation ( "open" );
	}
	public function savePost() {
		if (! Request::hasVar ( "module_folder" ) or ! Request::hasVar ( "version" ) or ! Request::hasVar ( "main_class" )) {
			Request::redirect ( ModuleHelper::buildActionURL ( "modstarter_new" ) );
		}
		$module_folder = Request::getVar ( "module_folder" );
		$version = Request::getVar ( "version" );
		$source = Request::getVar ( "source" );
		$embeddable = Request::hasVar ( "embeddable" );
		$shy = Request::hasVar ( "shy" );
		$main_class = Request::getVar ( "main_class" );
		$create_post_install_script = Request::hasVar ( "create_post_install_script" );
		$hooks = Request::hasVar ( "hooks" ) ? Request::getVar ( "hooks" ) : array ();
		// Modul erstellen oder updaten, sofern es schon existiert und eine modstarter Datei hat
	}
}