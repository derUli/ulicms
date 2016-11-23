<?php
class ActionRegistry {
	private static $assignedControllers = array ();
	public static function loadModuleActions() {
		if(!defined("KCFINDER_PAGE")){
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
			self::loadModuleActionAssignment ();
	}
	}
	public static function loadModuleActionAssignment() {
		$modules = getAllModules ();
		foreach ( $modules as $module ) {
			$action_controllers = getModuleMeta ( $module, "action_controllers" );
			if ($action_controllers) {
				foreach ( $action_controllers as $key => $value ) {
					self::$assignedControllers [$key] = $value;
				}
			}
		}
	}
	public static function assignControllerToAction($action, $controller) {
		self::$assignedControllers [$action] = $controller;
	}
	public static function getController() {
		$action = get_action ();
		if ($action and isset ( self::$assignedControllers [$action] )) {
			return ControllerRegistry::get ( self::$assignedControllers [$action] );
		} else {
			return null;
		}
	}
}
