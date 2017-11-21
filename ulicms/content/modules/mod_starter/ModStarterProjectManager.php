<?php
class ModStarterProjectManager {
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
}