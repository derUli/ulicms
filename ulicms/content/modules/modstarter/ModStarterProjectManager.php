<?php
class ModStarterProjectManager {
	const MODULE_NAME = "modstarter";
	public function getAllProjects() {
		$modules = getAllModules ();
		$result = array ();
		foreach ( $modules as $module ) {
			if (file_exists ( ModuleHelper::buildRessourcePath ( $module, ".modstarter" ) )) {
				$result [] = $module;
			}
		}
		return $result;
	}
	public function prepareMainClass($vars) {
		$mainClassFileName = ModuleHelper::buildRessourcePath ( self::MODULE_NAME, "templates/modbase/MainClass.tpl" );
		$mainClass = File::read ( $mainClassFileName );
		foreach ( $vars as $key => $value ) {
			$mainClass = str_replace ( "[[" . $key . "]]", $value, $mainClass );
		}
		return $mainClass;
	}
}