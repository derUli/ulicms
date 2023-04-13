<?php

declare(strict_types=1);

namespace App\Registries;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

use App\Controllers\Controller;
use ControllerRegistry;
use ModuleManager;

class ActionRegistry
{
    private static $actions = [];

    private static $assignedControllers = [];

    private static $defaultCoreActions = [
        'module_settings' => 'inc/module_settings.php',
        'groups' => 'inc/groups.php'
    ];

    private static $actionPermissions = [];

    public static function getDefaultCoreActions(): array
    {
        return self::$defaultCoreActions;
    }

    // load module backend action pages
    public static function loadModuleActions(): void
    {
        self::$actions = [];
        $coreActions = self::getDefaultCoreActions();

        foreach ($coreActions as $key => $value) {
            self::$actions[$key] = ULICMS_ROOT . "/admin/{$value}";
        }

        $moduleManager = new ModuleManager();
        $modules = $moduleManager->getEnabledModuleNames();

        foreach ($modules as $module) {
            $cActions = getModuleMeta($module, 'views') ?? getModuleMeta($module, 'actions');

            if ($cActions) {
                foreach ($cActions as $key => $value) {
                    $path = getModulePath($module, true) .
                            trim($value, '/');
                    $path = str_ends_with($path, '.php') ? $path : "{$path}.php";

                    self::$actions[$key] = $path;
                }
            }
        }
        self::loadModuleActionAssignment();
        self::loadActionPermissions();
    }

    public static function getActionPermission(string $action): ?string
    {
        $permission = null;
        if (isset(self::$actionPermissions[$action]) &&
                is_string(self::$actionPermissions[$action])) {
            $permission = self::$actionPermissions[$action];
        }
        return $permission;
    }

    // load action => controller assignments of modules
    public static function loadModuleActionAssignment(): void
    {
        $modules = getAllModules();
        foreach ($modules as $module) {
            $action_controllers = getModuleMeta($module, 'action_controllers');
            if (! $action_controllers) {
                continue;
            }
            foreach ($action_controllers as $key => $value) {
                self::$assignedControllers[$key] = $value;
            }
        }
    }

    public static function assignControllerToAction(
        string $action,
        string $controller
    ): void {
        self::$assignedControllers[$action] = $controller;
    }

    // return the controller for this action page
    public static function getController(): ?Controller
    {
        $action = get_action();
        if ($action && isset(self::$assignedControllers[$action])) {
            return ControllerRegistry::get(
                self::$assignedControllers[$action]
            );
        }
        return null;
    }

    /**
     * Get all actions
     * @return array
     */
    public static function getActions(): array
    {
        return self::$actions;
    }

    /**
     * Get action
     * @param string $action
     * @return string|null
     */
    public static function getAction(string $action): ?string
    {
        return self::$actions[$action] ?? null;
    }

    // load backend action page permission of modules
    private static function loadActionPermissions(): void
    {
        $moduleManager = new ModuleManager();
        $modules = $moduleManager->getEnabledModuleNames();

        foreach ($modules as $module) {
            $action_permissions = getModuleMeta($module, 'action_permissions');
            if (! $action_permissions) {
                continue;
            }
            foreach ($action_permissions as $action => $permission) {
                self::$actionPermissions[$action] = $permission;
            }
        }
    }
}
