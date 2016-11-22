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
				$classInstance = new $key ();
				if ($classInstance instanceof Controller) {
					self::$controllers [$key] = $classInstance;
				}
			}
		}
	}
	public static function get($class = null) {
		if ($class == null and get_action ()) {
			return ActionRegistry::getController ();
		} else if (isset ( self::$controllers [$class] )) {
			return self::$controllers [$class];
		} else {
			return null;
		}
	}
}