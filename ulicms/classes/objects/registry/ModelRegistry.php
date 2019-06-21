<?php

use UliCMS\Exceptions\FileNotFoundException;

class ModelRegistry {

    private static $objects = [];

    public static function loadModuleModels() {
        if (!defined("KCFINDER_PAGE")) {
            $modelRegistry = [];
            $modules = getAllModules();
            $disabledModules = Vars::get("disabledModules");
            foreach ($modules as $module) {
                if (faster_in_array($module, $disabledModules)) {
                    continue;
                }
                $models = getModuleMeta($module, "models") ? getModuleMeta($module, "models") : getModuleMeta($module, "objects");
                if ($models) {
                    foreach ($models as $key => $value) {
                        $path = getModulePath($module, true) . trim($value, "/");
                        if (!endsWith($path, ".php")) {
                            $path .= ".php";
                        }
                        $modelRegistry[$key] = $path;
                    }
                }
            }
            foreach ($modelRegistry as $key => $value) {
                if (file_exists($value)) {
                    require $value;
                } else {
                    throw new FileNotFoundException("Module {$module}: File '{$path}' not found.");
                }
            }
        }
    }

}
