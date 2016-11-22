<?php
class HelperRegistry {
	private static $helpers = array ();
	public static function loadModuleHelpers() {
		$helperRegistry = array ();
		$modules = getAllModules ();
		foreach ( $modules as $module ) {
			$helpers = getModuleMeta ( $module, "helpers" );
			if ($helpers) {
				foreach ( $helpers as $key => $value ) {
					$path = getModulePath ( $module ) . trim ( $value, "/" );
					if (! endsWith ( $path, ".php" )) {
						$path .= ".php";
					}
					$helperRegistry [$key] = $path;
				}
			}
		}
		foreach ( $helperRegistry as $key => $value ) {
			include_once $value;
			if (class_exists ( $key )) {
				$classInstance = new $key ();
				if ($classInstance instanceof Helper) {
					self::$helpers [$key] = $classInstance;
				}
			}
		}
	}
}