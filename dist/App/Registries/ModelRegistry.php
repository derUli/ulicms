<?php

declare(strict_types=1);

namespace App\Registries;

use Vars;
use ModuleManager;

use function getAllModules;
use function getModuleMeta;
use function getModulePath;
use function str_ends_with;

// loads model files of modules
class ModelRegistry
{
    // TODO: refactor this and split int into multiple method s
    public static function loadModuleModels(): void
    {
        $modelRegistry = [];

        $moduleManager = new ModuleManager();
        $modules = $moduleManager->getEnabledModuleNames();

        foreach ($modules as $module) {
            $models = getModuleMeta($module, "models") ?
                    getModuleMeta($module, "models") : getModuleMeta($module, "objects");
            if (!$models) {
                continue;
            }

            foreach ($models as $key => $value) {
                $path = getModulePath($module, true) . trim($value, '/');
                if (!str_ends_with($path, ".php")) {
                    $path .= ".php";
                }
                $modelRegistry[$key] = $path;
            }
        }

        foreach ($modelRegistry as $key => $value) {
            require_once $value;
        }
    }
}
