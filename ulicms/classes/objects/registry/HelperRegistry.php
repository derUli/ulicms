<?php

declare(strict_types=1);

namespace UliCMS\Registries;

use UliCMS\Exceptions\FileNotFoundException;
use Vars;
use function getAllModules;
use function faster_in_array;
use function getModuleMeta;
use function endsWith;
use function getModulePath;

// This method loads all module's helper classes
class HelperRegistry
{
    private static $helpers = [];

    // TODO: This code works but looks like crap
    // refactor it and split it into multiple small methods
    public static function loadModuleHelpers(): void
    {
        if (!defined("RESPONSIVE_FM")) {
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
                        $path = getModulePath($module, true) .
                                trim($value, "/");
                        if (!endsWith($path, ".php")) {
                            $path .= ".php";
                        }
                        $helperRegistry[$key] = $path;
                    }
                }
            }
            foreach ($helperRegistry as $key => $value) {
                if (file_exists($value)) {
                    require_once $value;
                } else {
                    throw new FileNotFoundException("Module {$module}: "
                            . "File '{$value}' not found.");
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
