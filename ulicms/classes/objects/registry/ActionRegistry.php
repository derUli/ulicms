<?php

declare(strict_types=1);

use UliCMS\Exceptions\FileNotFoundException;

class ActionRegistry {

    private static $actions = [];
    private static $assignedControllers = [];
    private static $defaultCoreActions = array(
        "module_settings" => "inc/module_settings.php",
        "groups" => "inc/groups.php"
    );
    private static $actionPermissions = [];

    public static function getDefaultCoreActions(): array {
        return self::$defaultCoreActions;
    }

    public static function loadModuleActions(): void {
        self::$actions = [];

        if (!defined("KCFINDER_PAGE")) {
            $coreActions = self::getDefaultCoreActions();
            foreach ($coreActions as $action => $file) {
                $path = $file;
                if (!endsWith($path, ".php")) {
                    $path .= ".php";
                }
                if (file_exists($path)) {
                    self::$actions[$action] = $file;
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
                        if (!endsWith($path, ".php")) {
                            $path .= ".php";
                        }

                        if (file_exists($path)) {
                            self::$actions[$key] = $path;
                        } else {
                            throw new FileNotFoundException("Module {$module}: File '{$path}' not found.");
                        }
                    }
                }
            }
            self::loadModuleActionAssignment();
            self::loadActionPermissions();
        }
    }

    private static function loadActionPermissions(): void {
        $modules = getAllModules();
        $disabledModules = Vars::get("disabledModules");
        foreach ($modules as $module) {
            if (faster_in_array($module, $disabledModules)) {
                continue;
            }
            $action_permissions = getModuleMeta($module, "action_permissions");
            if (!$action_permissions) {
                continue;
            }
            foreach ($action_permissions as $action => $permission) {
                self::$actionPermissions[$action] = $permission;
            }
        }
    }

    public static function getActionPermission(string $action): ?string {
        $permission = null;
        if (isset(self::$actionPermissions[$action]) and is_string(self::$actionPermissions[$action])) {
            $permission = self::$actionPermissions[$action];
        }
        return $permission;
    }

    public static function loadModuleActionAssignment(): void {
        $modules = getAllModules();
        foreach ($modules as $module) {
            $action_controllers = getModuleMeta($module, "action_controllers");
            if (!$action_controllers) {
                continue;
            }
            foreach ($action_controllers as $key => $value) {
                self::$assignedControllers[$key] = $value;
            }
        }
    }

    public static function assignControllerToAction(string $action, string $controller): void {
        self::$assignedControllers[$action] = $controller;
    }

    public static function getController(): ?Controller {

        $action = get_action();
        if ($action and isset(self::$assignedControllers[$action])) {
            return ControllerRegistry::get(self::$assignedControllers[$action]);
        }
        return null;
    }

    public static function getActions(): array {
        return self::$actions;
    }

    public static function getAction(string $action): ?string {
        return isset(self::$actions[$action]) ?
                self::$actions[$action] : null;
    }

}
