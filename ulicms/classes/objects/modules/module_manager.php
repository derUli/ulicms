<?php
class ModuleManager {
	public function getAllModules() {
		$sql = "select name from {prefix}modules";
		$query = Database::query ( $sql, true );
		$modules = array ();
		while ( $row = Database::fetchObject ( $query ) ) {
			$modules [] = new Module ( $row->name );
		}
		return $modules;
	}
	public function getEnabledModuleNames() {
		$sql = "select name from {prefix}modules where enabled = 1";
		$query = Database::query ( $sql, true );
		$modules = array ();
		while ( $row = Database::fetchObject ( $query ) ) {
			$modules [] = $row->name;
		}
		return $modules;
	}
	public function getDisabledModuleNames() {
		$sql = "select name from {prefix}modules where enabled = 0";
		$query = Database::query ( $sql, true );
		$modules = array ();
		while ( $row = Database::fetchObject ( $query ) ) {
			$modules [] = $row->name;
		}
		return $modules;
	}
	public function getAllModuleNames() {
		$sql = "select name from {prefix}modules";
		$query = Database::query ( $sql, true );
		$modules = array ();
		while ( $row = Database::fetchObject ( $query ) ) {
			$modules [] = $row->name;
		}
		return $modules;
	}
	public function getDependentModules($module, $allDeps = array()) {
		$dependencies = getModuleMeta ( $module, "dependencies" );
		if ($dependencies) {
			foreach ( $dependencies as $dep ) {
				$allDeps [] = $dep;
				$allDeps [] = $this->getDependentModules ( $dep, $allDeps );
			}
		}
		$allDeps = array_unique ( $allDeps );
		return $allDeps;
	}
	public function getEnabledDependentModules($module, $allDeps = array()) {
		$dependencies = getModuleMeta ( $module, "dependencies" );
		$enabledMods = $this->getEnabledModuleNames ();
		if ($dependencies) {
			foreach ( $dependencies as $dep ) {
				if (in_array ( $dep, $enabledMods )) {
					$allDeps [] = $dep;
					$allDeps [] = $this->getDependentModules ( $dep, $allDeps );
				}
			}
		}
		$allDeps = array_unique ( $allDeps );
		return $allDeps;
	}
	// Diese Funktion synchronisiert die modules in der Datenbank mit den modules im Modulordner
	// - Neue Module werden erfassen
	// - Versionsupdates erfassen
	// - Nicht mehr vorhandene Module aus Datenbank löschen
	// - neue Module sollen erst mal deaktiviert sein
	// - Diese Funktion aufrufen beim installieren von Modulen, beim leeren des Caches und beim deinstallieren von Modulen
	public function sync() {
		$realModules = getAllModules ();
		
		$dataBaseModules = $this->getAllModuleNames ();
		// Nicht mehr vorhandene Module entfernen
		foreach ( $dataBaseModules as $dbModule ) {
			
			if (! in_array ( $dbModule, $realModules )) {
				
				$module = new Module ( $dbModule );
				$module->delete ();
			}
		}
		$dataBaseModules = $this->getAllModuleNames ();
		foreach ( $realModules as $realModule ) {
			$version = getModuleMeta ( $realModule, "version" );
			if (in_array ( $realModule, $dataBaseModules )) {
				$module = new Module ( $realModule );
				if ($module->getVersion () != $version) {
					$module->setVersion ( $version );
				}
				$module->save ();
			} else {
				$module = new Module ();
				$module->setName ( $realModule );
				$module->setVersion ( $version );
				$module->save ();
				$module->enable ();
			}
		}
	}
}
