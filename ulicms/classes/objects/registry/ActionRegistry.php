<?php

class ActionRegistry
{

    private static $assignedControllers = array();

    private static $defaultCoreActions = array(
        "groups" => "inc/groups.php",
        "modules" => "inc/modules.php",
        "available_modules" => "inc/available_modules.php",
        "module_settings" => "inc/module_settings.php"
    );

    private static $actionPermissions = array();

    public static function getDefaultCoreActions()
    {
        return self::$defaultCoreActions;
    }

    public static function loadModuleActions()
    {
        if (! defined("KCFINDER_PAGE")) {
            global $actions;
            $coreActions = self::getDefaultCoreActions();
            foreach ($coreActions as $action => $file) {
                $path = $file;
                if (! endsWith($path, ".php")) {
                    $path .= ".php";
                }
                if (is_file($path)) {
                    $actions[$action] = $file;
                }
            }
            $modules = getAllModules();
            $disabledModules = Vars::get("disabledModules");
            foreach ($modules as $module) {
                if (faster_in_array($module, $disabledModules)) {
                    continue;
                }
                $cActions = getModuleMeta($module, "views") ? getModuleMeta($module, "views") : getModuleMeta($module, "actions");
                if ($cActions) {
                    foreach ($cActions as $key => $value) {
                        $path = getModulePath($module, true) . trim($value, "/");
                        if (! endsWith($path, ".php")) {
                            $path .= ".php";
                        }
                        
                        if (is_file($path)) {
                            $actions[$key] = $path;
                        }
                    }
                }
            }
            self::loadModuleActionAssignment();
            self::loadActionPermissions();
        }
    }

    private static function loadActionPermissions()
    {
        $modules = getAllModules();
        $disabledModules = Vars::get("disabledModules");
        foreach ($modules as $module) {
            if (faster_in_array($module, $disabledModules)) {
                continue;
            }
            $action_permissions = getModuleMeta($module, "action_permissions");
            if ($action_permissions) {
                foreach ($action_permissions as $action => $permission) {
                    self::$actionPermissions[$action] = $permission;
                }
            }
        }
    }

    public static function getActionPermission($action)
    {
        $permission = null;
        if (isset(self::$actionPermissions[$action]) and is_string(self::$actionPermissions[$action])) {
            $permission = self::$actionPermissions[$action];
        }
        return $permission;
    }

    public static function loadModuleActionAssignment()
    {
        $modules = getAllModules();
        foreach ($modules as $module) {
            $action_controllers = getModuleMeta($module, "action_controllers");
            if ($action_controllers) {
                foreach ($action_controllers as $key => $value) {
                    self::$assignedControllers[$key] = $value;
                }
            }
        }
    }

    public static function assignControllerToAction($action, $controller)
    {
        self::$assignedControllers[$action] = $controller;
    }

    public static function getController()
    {
        $action = get_action();
        if ($action and isset(self::$assignedControllers[$action])) {
            return ControllerRegistry::get(self::$assignedControllers[$action]);
        } else {
            return null;
        }
    }
}
