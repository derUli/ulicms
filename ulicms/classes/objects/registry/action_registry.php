<?php
class ActionRegistry {
	private static $assignedControllers = array ();
	private static $defaultCoreActions = array (
			"groups" => "inc/groups.php",
			"modules" => "inc/modules.php",
			"available_modules" => "inc/available_modules.php",
			"forms" => "inc/forms.php",
			"forms_new" => "inc/forms_new.php",
			"forms_edit" => "inc/forms_edit.php",
			"module_settings" => "inc/module_settings.php" 
	);
	public static function getDefaultCoreActions() {
		return self::$defaultCoreActions;
	}
	public static function loadModuleActions() {
		if (! defined ( "KCFINDER_PAGE" )) {
			global $actions;
			$coreActions = self::getDefaultCoreActions ();
			foreach ( $coreActions as $action => $file ) {
				$path = $file;
				if (! endsWith ( $path, ".php" )) {
					$path .= ".php";
				}
				if (file_exists ( $path )) {
					$actions [$action] = $file;
				}
			}
			$modules = getAllModules ();
			$disabledModules = Vars::get ( "disabledModules" );
			foreach ( $modules as $module ) {
				if (faster_in_array ( $module, $disabledModules )) {
					continue;
				}
				$cActions = getModuleMeta ( $module, "views" ) ? getModuleMeta ( $module, "views" ) : getModuleMeta ( $module, "actions" );
				if ($cActions) {
					foreach ( $cActions as $key => $value ) {
						$path = getModulePath ( $module, true ) . trim ( $value, "/" );
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
