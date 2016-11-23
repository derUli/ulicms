<?php
class ObjectRegistry {
	private static $objects = array ();
	public static function loadModuleObjects() {
		$objectRegistry = array ();
		$modules = getAllModules ();
		foreach ( $modules as $module ) {
			$objects = getModuleMeta ( $module, "objects" );
			if ($objects) {
				foreach ( $objects as $key => $value ) {
					$path = getModulePath ( $module ) . trim ( $value, "/" );
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