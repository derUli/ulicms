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
		
		$moduleFolderPath = getModulePath ( $module_folder, false );
		if (! file_exists ( $moduleFolderPath )) {
			mkdir ( $moduleFolderPath );
		}
		$baseDirs = array (
				ModuleHelper::buildRessourcePath ( $module_folder, "controllers" ),
				ModuleHelper::buildRessourcePath ( $module_folder, "objects" ),
				ModuleHelper::buildRessourcePath ( $module_folder, "templates" ) 
		);
		foreach ( $baseDirs as $dir ) {
			if (! file_exists ( $dir )) {
				mkdir ( $dir );
			}
			/*
			 * $keepFile = $dir . "/.keep";
			 * if (! file_exists ( $keepFile )) {
			 * File::write ( $keepFile, "" );
			 * }
			 */
		}
		
		$metadataFile = ModuleHelper::buildRessourcePath ( $module_folder, "metadata.json" );
		$metadata = array ();
		if (file_exists ( $metadataFile )) {
			$metadata = getModuleMeta ( $metadataFile );
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
			$metadata ["controllers"] = array (
					$main_class => "controllers/" . $main_class . ".php" 
			);
			$hooksCode = "";
			if ($embeddable) {
				$hooksCode .= "public function render(){\r\n\t\treturn \"\";\r\n\t}\r\n";
			}
			if (is_array ( $hooks )) {
				foreach ( $hooks as $hook ) {
					$hooksCode .= "\tpublic function $hook(){\r\n\t\t\r\n\t}\r\n";
				}
			}
			
			$mainClassCode = $manager->prepareMainClass ( array (
					"MainClass" => $main_class,
					"ModuleName" => str_replace ( "\"", "\\\"", $module_folder ),
					"Hooks" => $hooksCode 
			) );
			$mainClassFile = ModuleHelper::buildRessourcePath ( $module_folder, "controllers/" . $main_class . ".php" );
			File::write ( $mainClassFile, $mainClassCode );
		}
		File::write ( $metadataFile, json_readable_encode ( $metadata, 0, true ) );
		File::write ( ModuleHelper::buildRessourcePath ( $module_folder, ".modstarter" ), "" );
	}
}