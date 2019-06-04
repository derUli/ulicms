<?php

// This class is a work in progress
// It's currently only used for overriding of translation
class Translation {

    private static $translations = null;

    public static function init() {
        $translations = [];
    }

    public static function set($key, $value) {
        $key = "translation_" . $key;
        $key = strtoupper($key);
        self::$translations[$key] = $value;
    }

    public static function override($key, $value) {
        self::set($key, $value);
    }

    public static function get($key) {
        $retval = null;
        if (isset(self::$translations[$key])) {
            $retval = self::$translations[$key];
        }
        return $retval;
    }

    public static function includeCustomLangFile($lang) {
        $file = ULICMS_ROOT . "/lang/custom/" . basename($lang) . ".php";
        if (is_file($file)) {
            require_once $file;
        }
    }

    public static function loadAllModuleLanguageFiles($lang) {
        $modules = getAllModules();
        foreach ($modules as $module) {
            $file = getModulePath($module, true) . "/lang/" . $lang . ".php";

            if (is_file($file)) {
                require_once $file;
            } else {
                $file = getModulePath($module, true) . "/lang/en.php";

                if (is_file($file)) {
                    require_once $file;
                }
            }
        }
    }

    public static function loadCurrentThemeLanguageFiles($lang) {
        $modules = getAllModules();
        $file = getTemplateDirPath(get_theme(), true) . "/lang/" . $lang . ".php";
        if (is_file($file)) {
            require_once $file;
        } else {
            $file = getTemplateDirPath(get_theme(), true) . "/lang/en.php";
            if (is_file($file)) {
                require_once $file;
            }
        }
    }

}
