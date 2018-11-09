<?php

class ControllerRegistry
{

    private static $controllers = array();

    private static $controller_function_permissions = array();

    public static function loadModuleControllers()
    {
        if (! defined("KCFINDER_PAGE")) {
            $controllerRegistry = array();
            $modules = getAllModules();
            $disabledModules = Vars::get("disabledModules");
            foreach ($modules as $module) {
                if (faster_in_array($module, $disabledModules)) {
                    continue;
                }
                $controllers = getModuleMeta($module, "controllers");
                if ($controllers) {
                    foreach ($controllers as $key => $value) {
                        $path = getModulePath($module, true) . trim($value, "/");
                        if (! endsWith($path, ".php")) {
                            $path .= ".php";
                        }
                        $controllerRegistry[$key] = $path;
                    }
                }
                
                $controller_function_permissions = getModuleMeta($module, "controller_function_permissions");
                if ($controller_function_permissions) {
                    foreach ($controller_function_permissions as $key => $value) {
                        self::$controller_function_permissions[$key] = $value;
                    }
                }
            }
            foreach ($controllerRegistry as $key => $value) {
                include $value;
                if (class_exists($key)) {
                    $classInstance = new $key();
                    if ($classInstance instanceof Controller) {
                        self::$controllers[$key] = $classInstance;
                    }
                }
            }
        }
    }

    public static function runMethods()
    {
        if (isset($_REQUEST["sClass"]) and StringHelper::isNotNullOrEmpty($_REQUEST["sClass"])) {
            if (self::get($_REQUEST["sClass"])) {
                $sClass = $_REQUEST["sClass"];
                self::get($sClass)->runCommand();
            } else {
                
                $sClass = $_REQUEST["sClass"];
                throw new BadMethodCallException("class " . htmlspecialchars($sClass) . " not found");
            }
        }
    }

    public static function get($class = null)
    {
        if ($class == null and get_action()) {
            return ActionRegistry::getController();
        } else if (isset(self::$controllers[$class])) {
            return self::$controllers[$class];
        } else {
            return null;
        }
    }

    // check if user is permitted to call controller method $sMethod in Class $sClass
    public static function userCanCall($sClass, $sMethod)
    {
        $allowed = true;
        $acl = new ACL();
        if (isset(self::$controller_function_permissions[$sClass . "::" . $sMethod]) and StringHelper::isNotNullOrWhitespace(self::$controller_function_permissions[$sClass . "::" . $sMethod])) {
            $allowed = $acl->hasPermission(self::$controller_function_permissions[$sClass . "::" . $sMethod]);
        }
        return $allowed;
    }
}
