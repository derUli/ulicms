<?php

class UpdateManagerDashboard {

    public static function anyUpdateAvailable() {
        $pkg = new PackageManager();
        $modules = getAllModules();
        if (count($modules) > 0) {
            foreach ($modules as $module) {
                $version = getModuleMeta($module, "version");
                if ($version != null) {
                    $status = $pkg->checkForNewerVersionOfPackage($module);
                    if (version_compare($status, $version, '>')) {
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
                    if (version_compare($status, $version, '>')) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

}
