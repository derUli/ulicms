<?php

declare(strict_types=1);

namespace UliCMS\Registries;

use Vars;
use UliCMS\Exceptions\FileNotFoundException;
use function getAllModules;
use function getModuleMeta;
use function faster_in_array;
use function getModulePath;


// loads model files of modules
class ModelRegistry {

    // TODO: refactor this and split int into multiple method s
    public static function loadModuleModels(): void {
        if (!defined("RESPONSIVE_FM")) {
            $modelRegistry = [];
            $modules = getAllModules();
            $disabledModules = Vars::get("disabledModules");
            foreach ($modules as $module) {
                if (faster_in_array($module, $disabledModules)) {
                    continue;
                }
                $models = getModuleMeta($module, "models") ?
                        getModuleMeta($module, "models") : getModuleMeta($module, "objects");
                if (!$models) {
                    continue;
                }
                foreach ($models as $key => $value) {
                    $path = getModulePath($module, true) . trim($value, "/");
                    if (!endsWith($path, ".php")) {
                        $path .= ".php";
                    }
                    $modelRegistry[$key] = $path;
                }
            }
            foreach ($modelRegistry as $key => $value) {
                if (file_exists($value)) {
                    require $value;
                } else {
                    throw new FileNotFoundException("Module {$module}: "
                                    . "File '{$value}' not found.");
                }
            }
        }
    }

}
