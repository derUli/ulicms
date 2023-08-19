<?php

declare(strict_types=1);

namespace App\Translations;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

/**
 * This class contains util methods for translation stuff
 */
class Translation {
    /**
     * Load languages files of all modules
     * @param string $lang
     * @return void
     */
    public static function loadAllModuleLanguageFiles(string $lang): void {
        $manager = new \App\Packages\ModuleManager();
        $modules = $manager->getEnabledModuleNames();

        foreach ($modules as $module) {
            $currentLanguageFile = getModulePath($module, true) . '/lang/' .
                    $lang . '.php';
            $englishLanguageFile = getModulePath($module, true) .
                    '/lang/en.php';

            $files = [
                $currentLanguageFile,
                $englishLanguageFile
            ];

            foreach ($files as $file) {
                if (is_file($file)) {
                    require_once $file;
                    break;
                }
            }
        }
    }

    /**
     * Load language files of the current
     * @param string $lang
     * @return void
     */
    public static function loadCurrentThemeLanguageFiles(string $lang): void {
        $files = [
            getTemplateDirPath(get_theme(), true) . "/lang/{$lang}.php",
            getTemplateDirPath(get_theme(), true) . '/lang/en.php'
        ];

        foreach ($files as $file) {
            if (is_file($file)) {
                require_once $file;
            }
        }
    }
}
