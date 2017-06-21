<?php
class ObjectRegistry {
	private static $objects = array ();
	public static function loadModuleObjects() {
		if (! defined ( "KCFINDER_PAGE" )) {
			$objectRegistry = array ();
			$modules = getAllModules ();
			$disabledModules = Vars::get ( "disabledModules" );
			foreach ( $modules as $module ) {
				if (faster_in_array ( $module, $disabledModules )) {
					continue;
				}
				$objects = getModuleMeta ( $module, "models" ) ? getModuleMeta ( $module, "models" ) : getModuleMeta ( $module, "objects" );
				if ($objects) {
					foreach ( $objects as $key => $value ) {
						$path = getModulePath ( $module, true ) . trim ( $value, "/" );
						if (! endsWith ( $path, ".php" )) {
							$path .= ".php";
						}
						$objectRegistry [$key] = $path;
					}
				}
			}
			foreach ( $objectRegistry as $key => $value ) {
				include_once $value;
			}
		}
	}
}
