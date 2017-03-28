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
	// Diese Funktion synchronisiert die modules in der Datenbank mit den modules im Modulordner
	// - Neue Module werden erfassen
	// - Versionsupdates erfassen
	// - Nicht mehr vorhandene Module aus Datenbank löschen
	// - neue Module sollen erst mal deaktiviert sein
	// - Diese Funktion aufrufen beim installieren von Modulen, beim leeren des Caches und beim deinstallieren von Modulen
	public function sync() {
		throw new NotImplementedException ( "Sync not implemented yet." );
	}
}