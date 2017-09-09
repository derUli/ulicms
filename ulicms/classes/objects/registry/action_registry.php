<?php
class ActionRegistry {
	private static $assignedControllers = array ();
	private static $defaultCoreActions = array (
			"contents" => "inc/contents.php",
			"pages" => "inc/pages.php",
			"restore_version" => "inc/restore_version.php",
			"view_diff" => "inc/view_diff.php",
			"categories" => "inc/categories.php",
			"pages_edit" => "inc/edit_page.php",
			"pages_new" => "inc/add_page.php",
			"clone_page" => "inc/clone_page.php",
			"banner" => "inc/banner.php",
			"banner_new" => "inc/banner_new.php",
			"banner_edit" => "inc/banner_edit.php",
			"admins" => "inc/admins.php",
			"groups" => "inc/groups.php",
			"admin_new" => "inc/admins_new.php",
			"admin_edit" => "inc/admins_edit.php",
			"settings_categories" => "inc/settings_categories.php",
			"homepage_title" => "inc/homepage_title.php",
			"motto" => "inc/motto.php",
			"meta_description" => "inc/meta_description.php",
			"meta_keywords" => "inc/meta_keywords.php",
			"spam_filter" => "inc/spamfilter_settings.php",
			"customize_menu" => "inc/customize_menu.php",
			"modules" => "inc/modules.php",
			"available_modules" => "inc/available_modules.php",
			"install_modules" => "inc/install_modules.php",
			"upload_patches" => "inc/upload_patches.php",
			"open_graph" => "inc/open_graph.php",
			"forms" => "inc/forms.php",
			"forms_new" => "inc/forms_new.php",
			"forms_edit" => "inc/forms_edit.php",
			"motd" => "inc/motd.php",
			"edit_profile" => "inc/edit_profile.php",
			"logo_upload" => "inc/logo.php",
			"languages" => "inc/languages.php",
			"cache" => "inc/cache_settings.php",
			"install_method" => "inc/install_method.php",
			"upload_package" => "inc/upload_package.php",
			"module_settings" => "inc/module_settings.php",
			"other_settings" => "inc/other_settings.php",
			"frontpage_settings" => "inc/frontpage.php",
			"pkg_settings" => "inc/pkg_settings.php",
			"available_patches" => "inc/available_patches.php",
			"install_patches" => "inc/install_patches.php",
			"videos" => "inc/videos.php",
			"add_video" => "inc/add_video.php",
			"edit_video" => "inc/edit_video.php",
			"audio" => "inc/audio.php",
			"add_audio" => "inc/add_audio.php",
			"edit_audio" => "inc/edit_audio.php",
			"do-post-install" => "inc/do-post-install.php",
			"pkginfo" => "inc/pkginfo.php",
			"sin_package_install_ok" => "inc/sin_package_install_ok.php" 
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
