<?php

namespace UliCMS\Registries;

use UliCMS\Exceptions\FileNotFoundException;
use Vars;
use function getAllModules;
use function faster_in_array;
use function getModuleMeta;
use function endsWith;
use function getModulePath;

class HelperRegistry {

    private static $helpers = [];

    public static function loadModuleHelpers() {
        if (!defined("KCFINDER_PAGE")) {
            $helperRegistry = [];
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
                        }
                        $helperRegistry[$key] = $path;
                    }
                }
            }
            foreach ($helperRegistry as $key => $value) {
                if (is_file($value)) {
                    require $value;
                } else {
                    throw new FileNotFoundException("Module {$module}: File '{$path}' not found.");
                }
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
