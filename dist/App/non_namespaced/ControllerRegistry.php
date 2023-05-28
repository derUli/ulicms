<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Controllers\Controller;
use App\Registries\ActionRegistry;
use App\Security\Permissions\PermissionChecker;

class ControllerRegistry {
    private static $controllers = [];

    private static $controller_function_permissions = [];

    // load and initialize all module controllers
    public static function loadModuleControllers(): void {
        $controllerRegistry = [];

        $moduleManager = new \App\Packages\ModuleManager();
        $modules = $moduleManager->getEnabledModuleNames();

        foreach ($modules as $module) {
            $controllers = getModuleMeta($module, 'controllers');
            if ($controllers) {
                foreach ($controllers as $key => $value) {
                    $path = getModulePath($module, true) .
                            trim($value, '/');

                    if (! str_ends_with($path, '.php')) {
                        $path .= '.php';
                    }

                    $controllerRegistry[$key] = $path;
                }
            }

            // read controller function permissions from metadata files of modules
            $controller_function_permissions = getModuleMeta(
                $module,
                'controller_function_permissions'
            );
            if ($controller_function_permissions) {
                foreach ($controller_function_permissions as $key => $value) {
                    self::$controller_function_permissions[$key] = $value;
                }
            }
        }
        foreach ($controllerRegistry as $key => $value) {
            include_once $value;

            if (class_exists($key)) {
                $classInstance = new $key();
                if ($classInstance instanceof Controller) {
                    self::$controllers[$key] = $classInstance;
                }
            }
        }
    }

    public static function runMethods(): void {
        if (isset($_REQUEST['sClass'])
                && ! empty($_REQUEST['sClass'])) {
            if (self::get($_REQUEST['sClass'])) {
                $sClass = $_REQUEST['sClass'];
                self::get($sClass)->runCommand();
            } else {
                $sClass = $_REQUEST['sClass'];
                throw new BadMethodCallException(
                    'class ' . _esc($sClass) . ' not found'
                );
            }
        }
    }

    // Return an instance of a controller by it's name
    // If $class is null it returns the main class for the current backend action if defined
    public static function get(?string $class = null): ?Controller {
        if ($class == null && get_action()) {
            return ActionRegistry::getController();
        } elseif (isset(self::$controllers[$class])) {
            return self::$controllers[$class];
        }
        return null;
    }

    // Check if user is permitted to call controller method $sMethod in Class $sClass
    public static function userCanCall(string $sClass, string $sMethod): bool {
        $allowed = true;
        $acl = PermissionChecker::fromCurrentUser();
        $methodIdentifier = $sClass . '::' . $sMethod;

        $wildcardMethodIdentifier = $sClass . '::*';

        if (
            isset(self::$controller_function_permissions[$methodIdentifier]) &&
            ! empty(self::$controller_function_permissions[$methodIdentifier])
        ) {
            $allowed = $acl->hasPermission(
                self::$controller_function_permissions[$methodIdentifier]
            );
        } elseif (
            isset(self::$controller_function_permissions[$wildcardMethodIdentifier]) &&
            ! empty(self::$controller_function_permissions[$wildcardMethodIdentifier])) {
            $allowed = $acl->hasPermission(
                self::$controller_function_permissions[$wildcardMethodIdentifier]
            );
        }
        return $allowed;
    }
}
