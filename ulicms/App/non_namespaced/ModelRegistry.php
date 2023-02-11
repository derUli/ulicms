<?php

declare(strict_types=1);

use App\Exceptions\FileNotFoundException;

// loads model files of modules
class ModelRegistry
{
    // TODO: refactor this and split int into multiple method s
    public static function loadModuleModels(): void
    {
        $modelRegistry = [];
        $modules = getAllModules();
        $disabledModules = Vars::get("disabledModules") ?? [];
        foreach ($modules as $module) {
            if (in_array($module, $disabledModules)) {
                continue;
            }
            $models = getModuleMeta($module, "models") ?
                    getModuleMeta($module, "models") : getModuleMeta($module, "objects");
            if (!$models) {
                continue;
            }
            foreach ($models as $key => $value) {
                $path = getModulePath($module, true) . trim($value, "/");
                if (!str_ends_with($path, ".php")) {
                    $path .= ".php";
                }
                $modelRegistry[$key] = $path;
            }
        }
        foreach ($modelRegistry as $key => $value) {
            if (is_file($value)) {
                require $value;
            } else {
                throw new FileNotFoundException("Module {$module}: "
                                . "File '{$value}' not found.");
            }
        }
    }
}
