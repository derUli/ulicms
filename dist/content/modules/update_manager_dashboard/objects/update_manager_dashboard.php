<?php

use App\Packages\PackageManager;

class UpdateManagerDashboard
{
    public static function anyUpdateAvailable()
    {
        $pkg = new PackageManager();
        $modules = getAllModules();
        if (count($modules) > 0) {
            foreach ($modules as $module) {
                $version = getModuleMeta($module, "version");
                if ($version != null) {
                    $status = $pkg->checkForNewerVersionOfPackage($module);
                    if (\App\Utils\VersionComparison::compare($status, $version, '>')) {
                        return true;
                    }
                }
            }
        }

        $themes = getAllThemes();
        if (count($themes) > 0) {
            foreach ($themes as $theme) {
                $version = getThemeMeta($theme, "version");
                if ($version != null) {
                    $theme = "theme-" . $theme;
                    $status = $pkg->checkForNewerVersionOfPackage($theme);
                    if (\App\Utils\VersionComparison::compare($status, $version, '>')) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
