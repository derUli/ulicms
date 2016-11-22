<?php
class ControllerRegistry {
	private static $controllers = array ();
	public static function loadModuleControllers() {
		$controllerRegistry = array ();
		$modules = getAllModules ();
		foreach ( $modules as $module ) {
			$controllers = getModuleMeta ( $module, "controllers" );
			if ($controllers) {
				foreach ( $controllers as $key => $value ) {
					$path = getModulePath ( $module ) . trim ( $value, "/" );
					if (! endsWith ( $path, ".php" )) {
						$path .= ".php";
					}
					$controllerRegistry [$key] = $path;
				}
			}
		}
		foreach ( $controllerRegistry as $key => $value ) {
			include_once $value;
			if (class_exists ( $key )) {
				$controllers [$key] = new $key ();
			}
		}
	}
	public static function get($class) {
		if (isset ( self::$controllers ["class"] )) {
			return self::$controllers ["class"];
		} else {
			return null;
		}
	}
}