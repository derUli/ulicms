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
		// TODO: Check if module_folder contains only valid chars
		$module_folder = Request::getVar ( "module_folder" );
		$version = Request::getVar ( "version" );
		$source = Request::getVar ( "source" );
		$embeddable = Request::hasVar ( "embeddable" );
		$shy = Request::hasVar ( "shy" );
		$main_class = Request::getVar ( "main_class" );
		$create_post_install_script = Request::hasVar ( "create_post_install_script" );
		$hooks = Request::hasVar ( "hooks" ) ? Request::getVar ( "hooks" ) : array ();
		// Modul erstellen oder updaten, sofern es schon existiert und eine modstarter Datei hat
		
		$moduleFolderPath = getModulePath ( $module_folder, true );
		if (! is_dir ( $moduleFolderpath )) {
			mkdir ( $moduleFolderPath );
		}
		$metadataFile = ModuleHelper::buildRessourcePath ( $module_folder, "metadata.json" );
		$metadata = array ();
		if (file_exists ( $metadataFile )) {
			$metadata = getModuleMeta ( $module_folder );
		}
		if (StringHelper::isNotNullOrWhitespace ( $version )) {
			$metadata ["version"] = $version;
		}
		if (StringHelper::isNotNullOrWhitespace ( $source )) {
			$metadata ["source"] = $source;
		}
		$metadata ["embed"] = $embeddable;
		$metadata ["shy"] = $shy;
		
		$manager = new ModStarterProjectManager ();
		
		if (StringHelper::isNotNullOrWhitespace ( $main_class )) {
			$metadata ["main_class"] = $main_class;
			$hooksCode = "";
			if (is_array ( $hooks )) {
				foreach ( $hooks as $hook ) {
					$hooksCode .= "function $hook(){\r\n}\r\n\r\n";
				}
			}
			$mainClassCode = $manager->prepareMainClass ( array (
					"MainClass" => $main_class,
					"ModuleName" => str_replace ( "\"", "\\\"", $module_folder ),
					"Hooks" => $hooksCode 
			) );
		}
	}
}