<?php

declare(strict_types=1);

namespace App\Registries;

use App\Exceptions\FileNotFoundException;
use Vars;

use function getAllModules;
use function getModuleMeta;
use function getModulePath;

// This method loads all module's helper classes
class HelperRegistry
{
    private static $helpers = [];

    // TODO: This code works but looks like crap
    // refactor it and split it into multiple small methods
    public static function loadModuleHelpers(): void
    {
        $helperRegistry = [];
        $modules = getAllModules();
        $disabledModules = Vars::get("disabledModules") ?? [];
        foreach ($modules as $module) {
            if (in_array($module, $disabledModules)) {
                continue;
            }
            $helpers = getModuleMeta($module, "helpers");
            if ($helpers) {
                foreach ($helpers as $key => $value) {
                    $path = getModulePath($module, true) .
                            trim($value, '/');
                    if (!str_ends_with($path, ".php")) {
                        $path .= ".php";
                    }
                    $helperRegistry[$key] = $path;
                }
            }
        }
        foreach ($helperRegistry as $key => $value) {
            if (is_file($value)) {
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
