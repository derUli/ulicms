<?php

declare(strict_types=1);

// This class is a work in progress
// It's currently only used for overriding of translation
class Translation {

    private static $translations = null;

    public static function init(): void {
        self::$translations = [];
    }

    public static function set(string $key, string $value): void {
        $key = "translation_" . $key;
        $key = strtoupper($key);
        self::$translations[$key] = $value;
    }

    public static function override(string $key, string $value): void {
        self::set($key, $value);
    }

    public static function get(string $key): ?string {
        $retval = null;
        if (isset(self::$translations[$key])) {
            $retval = self::$translations[$key];
        }
        return $retval;
    }

    public static function includeCustomLangFile(string $lang): void {
        $file = ULICMS_ROOT . "/lang/custom/" . basename($lang) . ".php";
        if (file_exists($file)) {
            require_once $file;
        }
    }

    public static function loadAllModuleLanguageFiles(string $lang): void {
        $modules = getAllModules();
        foreach ($modules as $module) {
            $currentLanguageFile = getModulePath($module, true) . "/lang/" . $lang . ".php";
            $englishLanguageFile = getModulePath($module, true) . "/lang/en.php";

            if (file_exists($currentLanguageFile)) {
                require_once $currentLanguageFile;
            } else if (file_exists($englishLanguageFile)) {
                require_once $englishLanguageFile;
            }
        }
    }

    public static function loadCurrentThemeLanguageFiles(string $lang): void {
        $file = getTemplateDirPath(get_theme(), true) . "/lang/" . $lang . ".php";
        if (file_exists($file)) {
            require_once $file;
            return;
        }
        $file = getTemplateDirPath(get_theme(), true) . "/lang/en.php";
        if (file_exists($file)) {
            require_once $file;
        }
    }

}
