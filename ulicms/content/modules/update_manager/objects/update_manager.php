<?php

declare(strict_types=1);

class UpdateManager {

    public static function getAllUpdateablePackages(): array {
        $pkg = new PackageManager();
        $retval = [];
        $modules = getAllModules();
        foreach ($modules as $module) {
            $version = getModuleMeta($module, "version");
            if ($version == null) {
                continue;
            }
            $status = $pkg->checkForNewerVersionOfPackage($module);
            if ($status and \UliCMS\Utils\VersionComparison\compare($status, $version, '>')) {
                $retval[] = $module . "-" . $status;
            }
        }

        $themes = getAllThemes();
        foreach ($themes as $theme) {
            $version = getThemeMeta($theme, "version");
            if ($version == null) {
                continue;
            }
            $theme = "theme-" . $theme;
            $status = $pkg->checkForNewerVersionOfPackage($theme);
            if ($status and \UliCMS\Utils\VersionComparison\compare($status, $version, '>')) {
                $retval[] = $theme . "-" . $status;
            }
        }

        return $retval;
    }

}
