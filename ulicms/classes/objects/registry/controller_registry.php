<?php
class ControllerRegistry {
	private static $controllers = array ();
	public static function loadModuleControllers() {
		if (! defined ( "KCFINDER_PAGE" )) {
			$controllerRegistry = array ();
			$modules = getAllModules ();
			$disabledModules = Vars::get ( "disabledVars" );
			foreach ( $modules as $module ) {
				if (in_array ( $module, $disabledModules )) {
					continue;
				}
				$controllers = getModuleMeta ( $module, "controllers" );
				if ($controllers) {
					foreach ( $controllers as $key => $value ) {
						$path = getModulePath ( $module, true ) . trim ( $value, "/" );
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
	}
	public static function runMethods() {
		if (isset ( $_REQUEST ["sClass"] ) and isNotNullOrEmpty ( $_REQUEST ["sClass"] )) {
			if (self::get ( $_REQUEST ["sClass"] )) {
				$sClass = $_REQUEST ["sClass"];
				self::get ( $sClass )->runCommand ();
			} else {
				throw new BadMethodCallException ( "class " . htmlspecialchars ( $sClass ) . " not found" );
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
