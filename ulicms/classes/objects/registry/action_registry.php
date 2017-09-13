<?php
class ActionRegistry {
	private static $assignedControllers = array ();
	private static $defaultCoreActions = array (
			"categories" => "inc/categories.php",
			"banner" => "inc/banner.php",
			"banner_new" => "inc/banner_new.php",
			"banner_edit" => "inc/banner_edit.php",
			"admins" => "inc/admins.php",
			"groups" => "inc/groups.php",
			"admin_new" => "inc/admins_new.php",
			"admin_edit" => "inc/admins_edit.php",
			"modules" => "inc/modules.php",
			"available_modules" => "inc/available_modules.php",
			"install_modules" => "inc/install_modules.php",
			"upload_patches" => "inc/upload_patches.php",
			"forms" => "inc/forms.php",
			"forms_new" => "inc/forms_new.php",
			"forms_edit" => "inc/forms_edit.php",
			"edit_profile" => "inc/edit_profile.php",
			"install_method" => "inc/install_method.php",
			"upload_package" => "inc/upload_package.php",
			"module_settings" => "inc/module_settings.php",
			"available_patches" => "inc/available_patches.php",
			"install_patches" => "inc/install_patches.php",
			"edit_video" => "inc/edit_video.php",
			"audio" => "inc/audio.php",
			"add_audio" => "inc/add_audio.php",
			"edit_audio" => "inc/edit_audio.php"
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
