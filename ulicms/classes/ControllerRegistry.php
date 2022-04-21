<?php

declare(strict_types=1);

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use UliCMS\Exceptions\FileNotFoundException;
use UliCMS\Registries\ActionRegistry;
use UliCMS\Storages\Vars;
use UliCMS\Security\PermissionChecker;

class ControllerRegistry {

    private static $controllers = [];
    private static $controller_function_permissions = [];

    // load and initialize all module controllers
    public static function loadModuleControllers(): void {
        if (!defined("RESPONSIVE_FM")) {
            $controllerRegistry = [];
            $modules = getAllModules();
            $disabledModules = Vars::get("disabledModules");
            foreach ($modules as $module) {
                if (faster_in_array($module, $disabledModules)) {
                    continue;
                }
                $controllers = getModuleMeta($module, "controllers");
                if ($controllers) {
                    foreach ($controllers as $key => $value) {
                        $path = getModulePath($module, true) .
                                trim($value, "/");
                        if (!endsWith($path, ".php")) {
                            $path .= ".php";
                        }
                        $controllerRegistry[$key] = $path;
                    }
                }

                // read controller function permissions from metadata files of modules
                $controller_function_permissions = getModuleMeta(
                        $module,
                        "controller_function_permissions"
                );
                if ($controller_function_permissions) {
                    foreach ($controller_function_permissions as $key => $value) {
                        self::$controller_function_permissions[$key] = $value;
                    }
                }
            }
            foreach ($controllerRegistry as $key => $value) {
                if (file_exists($value)) {
                    require_once $value;
                } else {
                    throw new FileNotFoundException("Module {$module}: "
                                    . "File '{$value}' not found.");
                }
                if (class_exists($key)) {
                    $classInstance = new $key();
                    if ($classInstance instanceof Controller) {
                        self::$controllers[$key] = $classInstance;
                    }
                }
            }
        }
    }

    public static function runMethods(): void {
        if (isset($_REQUEST["sClass"])
                && StringHelper::isNotNullOrEmpty($_REQUEST["sClass"])) {
            if (self::get($_REQUEST["sClass"])) {
                $sClass = $_REQUEST["sClass"];
                self::get($sClass)->runCommand();
            } else {
                $sClass = $_REQUEST["sClass"];
                throw new BadMethodCallException(
                                "class " . _esc($sClass) . " not found"
                );
            }
        }
    }

    //return an instance of a controller by it's name
    // if $class is null it returns the main class for the current backend action if defined
    public static function get(?string $class = null): ?Controller {
        if ($class == null && get_action()) {
            return ActionRegistry::getController();
        } elseif (isset(self::$controllers[$class])) {
            return self::$controllers[$class];
        }
        return null;
    }

    // check if user is permitted to call controller method $sMethod in Class $sClass
    public static function userCanCall(string $sClass, string $sMethod): bool {
        $allowed = true;
        $permissionChecker = new PermissionChecker(get_user_id());
        $methodIdentifier = $sClass . "::" . $sMethod;

        $wildcardMethodIdentifier = $sClass . "::*";

        if (
                isset(self::$controller_function_permissions[$methodIdentifier]) &&
                !is_blank(self::$controller_function_permissions[$methodIdentifier])
        ) {
            $allowed = $permissionChecker->hasPermission(
                    self::$controller_function_permissions[$methodIdentifier]
            );
        } elseif (
                isset(self::$controller_function_permissions[$wildcardMethodIdentifier]) &&
                !is_blank(self::$controller_function_permissions[$wildcardMethodIdentifier])) {
            $allowed = $permissionChecker->hasPermission(
                    self::$controller_function_permissions[$wildcardMethodIdentifier]
            );
        }
        return $allowed;
    }

}
