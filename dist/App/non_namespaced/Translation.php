<?php

declare(strict_types=1);

// This class is a work in progress
// It's currently only used for overriding of translations
class Translation
{
    private static $translations = [];

    public static function init(): void
    {
        self::$translations = [];
    }

    public static function set(string $key, ?string $value): void
    {
        $key = strtoupper("translation_" . $key);

        if ($value !== null) {
            self::$translations[$key] = $value;
        } else {
            unset(self::$translations[$key]);
        }
    }

    public static function delete(string $key): void
    {
        self::set($key, null);
    }

    public static function override(string $key, string $value): void
    {
        self::set($key, $value);
    }

    public static function get(string $key): ?string
    {
        $retval = null;

        if (isset(self::$translations[$key])) {
            $retval = self::$translations[$key];
        }

        return $retval;
    }

    public static function includeCustomLangFile(string $lang): void
    {
        $file = ULICMS_ROOT . "/lang/custom/" . basename($lang) . ".php";

        if (is_file($file)) {
            require_once $file;
        }
    }

    public static function loadAllModuleLanguageFiles(string $lang): void
    {
        $modules = getAllModules();

        foreach ($modules as $module) {
            $currentLanguageFile = getModulePath($module, true) . "/lang/" .
                    $lang . ".php";
            $englishLanguageFile = getModulePath($module, true) .
                    "/lang/en.php";

            $files = [
                $currentLanguageFile,
                $englishLanguageFile
            ];

            foreach ($files as $file) {
                if (is_file($file)) {
                    require_once $file;
                    continue;
                }
            }
        }
    }

    public static function loadCurrentThemeLanguageFiles(string $lang): void
    {
        $files = [
            getTemplateDirPath(get_theme(), true) . "/lang/{$lang}.php",
            getTemplateDirPath(get_theme(), true) . "/lang/en.php"
        ];

        foreach ($files as $file) {
            if (is_file($file)) {
                require_once $file;
            }
        }
    }
}
