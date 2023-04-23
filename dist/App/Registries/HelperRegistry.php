<?php

declare(strict_types=1);

namespace App\Registries;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use ModuleManager;

use function getModuleMeta;
use function getModulePath;

// This method loads all module's helper classes
class HelperRegistry {
    // TODO: This code works but looks like crap
    // refactor it and split it into multiple small methods
    public static function loadModuleHelpers(): void {
        $helperRegistry = [];
        $moduleManager = new ModuleManager();
        $modules = $moduleManager->getEnabledModuleNames();

        foreach ($modules as $module) {
            $helpers = getModuleMeta($module, 'helpers');
            if ($helpers) {
                foreach ($helpers as $key => $value) {
                    $path = getModulePath($module, true) .
                            trim($value, '/');
                    $path = str_ends_with($path, '.php') ? $path : $path . '.php';

                    $helperRegistry[$key] = $path;
                }
            }
        }
        foreach ($helperRegistry as $key => $value) {
            include_once $value;
        }
    }
}
