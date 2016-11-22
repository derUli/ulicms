<?php
class ActionRegistry {
	public static function loadModuleActions() {
		global $actions;
		$modules = getAllModules ();
		foreach ( $modules as $module ) {
			$cActions = getModuleMeta ( $module, "actions" );
			if ($cActions) {
				foreach ( $cActions as $key => $value ) {
					$path = getModulePath ( $module ) . trim ( $value, "/" );
					if (! endsWith ( $path, ".php" )) {
						$path .= ".php";
					}
					
					if (file_exists ( $path ) and is_file ( $path )) {
						$actions [$key] = $path;
					}
				}
			}
		}
	}
}