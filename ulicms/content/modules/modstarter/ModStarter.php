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
	public function editGet() {
		$name = Request::getVar ( "name" );
		$model = new ModStarterProjectViewModel ();
		
		ViewBag::set ( "model", $model );
		if (! $name) {
			Request::redirect ( ModuleHelper::buildAdminURL ( self::MODULE_NAME ) );
		}
		$metadata = getModuleMeta ( $name );
		if (! $metadata) {
			Request::redirect ( ModuleHelper::buildAdminURL ( self::MODULE_NAME ) );
		}
		
		$model->module_folder = $name;
		$model->source = isset ( $metadata ["source"] ) ? $metadata ["source"] : "";
		$model->version = $metadata ["version"];
		$model->embeddable = (isset ( $metadata ["embed"] ) and $metadata ["embed"]);
		$model->shy = (isset ( $metadata ["shy"] ) and $metadata ["shy"]);
		$model->main_class = $metadata ["main_class"];
		$model->create_post_install_script = file_exists ( Path::resolve ( "ULICMS_ROOT/post-install.php" ) );
		$model->hooks = is_array ( $metadata ["hooks"] ) ? $metadata ["hooks"] : array ();
		$model->edit = true;
		ViewBag::set ( "model", $model );
	}
	public function updatePost() {
		if (! Request::hasVar ( "module_folder" ) or ! Request::hasVar ( "version" ) or ! Request::hasVar ( "main_class" )) {
			Request::redirect ( ModuleHelper::buildActionURL ( "modstarter_new" ) );
		}
		$module_folder = basename ( Request::getVar ( "module_folder" ) );
		if ($module_folder == "." or $module_folder == "..") {
			Request::redirect ( ModuleHelper::buildActionURL ( "modstarter_new" ) );
		}
		$version = Request::getVar ( "version" );
		$source = Request::getVar ( "source" );
		$embeddable = Request::hasVar ( "embeddable" );
		$shy = Request::hasVar ( "shy" );
		$create_post_install_script = (Request::hasVar ( "create_post_install_script" ) and ! file_exists ( Path::resolve ( "ULICMS_ROOT/post-install.php" ) ));
		
		$moduleFolderPath = getModulePath ( $module_folder, false );
		if (! file_exists ( $moduleFolderPath )) {
			Request::redirect ( ModuleHelper::buildActionURL ( "modstarter_new" ) );
		}
		$baseDirs = array (
				ModuleHelper::buildRessourcePath ( $module_folder, "controllers" ),
				ModuleHelper::buildRessourcePath ( $module_folder, "objects" ),
				ModuleHelper::buildRessourcePath ( $module_folder, "templates" ),
				ModuleHelper::buildRessourcePath ( $module_folder, "lang" ),
				ModuleHelper::buildRessourcePath ( $module_folder, "sql" ),
				ModuleHelper::buildRessourcePath ( $module_folder, "sql/up" ),
				ModuleHelper::buildRessourcePath ( $module_folder, "sql/down" ) 
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
		
		File::write ( $metadataFile, json_readable_encode ( $metadata, 0, true ) );
		File::write ( ModuleHelper::buildRessourcePath ( $module_folder, ".modstarter" ), "" );
		if ($create_post_install_script) {
			$script = Path::resolve ( "ULICMS_ROOT/post-install.php" );
			File::write ( $script, "<?php\r\n" );
		}
		Request::redirect ( ModuleHelper::buildAdminURL ( self::MODULE_NAME ) );
	}
	public function createPost() {
		if (! Request::hasVar ( "module_folder" ) or ! Request::hasVar ( "version" ) or ! Request::hasVar ( "main_class" )) {
			Request::redirect ( ModuleHelper::buildActionURL ( "modstarter_new" ) );
		}
		$module_folder = basename ( Request::getVar ( "module_folder" ) );
		if ($module_folder == "." or $module_folder == "..") {
			Request::redirect ( ModuleHelper::buildActionURL ( "modstarter_new" ) );
		}
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
				ModuleHelper::buildRessourcePath ( $module_folder, "templates" ),
				ModuleHelper::buildRessourcePath ( $module_folder, "lang" ),
				ModuleHelper::buildRessourcePath ( $module_folder, "sql" ),
				ModuleHelper::buildRessourcePath ( $module_folder, "sql/up" ),
				ModuleHelper::buildRessourcePath ( $module_folder, "sql/down" ) 
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
		if ($create_post_install_script) {
			$script = Path::resolve ( "ULICMS_ROOT/post-install.php" );
			File::write ( $script, "<?php\r\n" );
		}
		Request::redirect ( ModuleHelper::buildAdminURL ( self::MODULE_NAME ) );
	}
}