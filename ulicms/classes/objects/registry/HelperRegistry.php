<?php

use UliCMS\Exceptions\FileNotFoundException;

class HelperRegistry {

    private static $helpers = array();

    public static function loadModuleHelpers() {
        if (!defined("KCFINDER_PAGE")) {
            $helperRegistry = array();
            $modules = getAllModules();
            $disabledModules = Vars::get("disabledModules");
            foreach ($modules as $module) {
                if (faster_in_array($module, $disabledModules)) {
                    continue;
                }
                $helpers = getModuleMeta($module, "helpers");
                if ($helpers) {
                    foreach ($helpers as $key => $value) {
                        $path = getModulePath($module, true) . trim($value, "/");
                        if (!endsWith($path, ".php")) {
                            $path .= ".php";
                        } else {
                            throw new FileNotFoundException("Module {$module}: File '{$path}' not found.");
                        }
                        $helperRegistry[$key] = $path;
                    }
                }
            }
            foreach ($helperRegistry as $key => $value) {
                include $value;
                if (class_exists($key)) {
                    $classInstance = new $key();
                    if ($classInstance instanceof Helper) {
                        self::$helpers[$key] = $classInstance;
                    }
                }
            }
        }
    }

}
