<?php

declare(strict_types=1);

use App\Utils\CacheUtil;

function getAllThemes(): array
{
    $pkg = new PackageManager();
    return $pkg->getInstalledPackages('themes');
}

// API-Aufruf zur Deinstallation eines Moduls
// Ruft uninstall Script auf, falls vorhanden
// Löscht anschließend den Ordner modules/$name
// TODO: dies in die PackageManager Klasse verschieben
function uninstall_module(string $name, string $type = "module"): bool
{
    $acl = new ACL();
    if (!$acl->hasPermission("install_packages") && !is_cli()) {
        return false;
    }

    $name = trim(basename(trim($name)));

    // Verhindern, dass der Modulordner oder gar das ganze
    // CMS gelöscht werden kann
    if ($name == "." or $name == ".." or empty($name)) {
        return false;
    }
    switch ($type) {
        case "module":
            $moduleDir = getModulePath($name, true);

            // Modul-Ordner entfernen
            if (is_dir($moduleDir)) {
                $uninstall_script = getModuleUninstallScriptPath($name, true);
                $uninstall_script2 = getModuleUninstallScriptPath2($name, true);

                // Uninstall Script ausführen, sofern vorhanden
                $mainController = ModuleHelper::getMainController($name);
                if ($mainController
                        and method_exists($mainController, "uninstall")) {
                    $mainController->uninstall();
                } elseif (is_file($uninstall_script)) {
                    require $uninstall_script;
                } elseif (is_file($uninstall_script2)) {
                    require $uninstall_script2;
                }
                sureRemoveDir($moduleDir, true);
                CacheUtil::clearCache();
                return !is_dir($moduleDir);
            }
            break;
        case "theme":
            $cTheme = Settings::get("theme");
            $allThemes = getAllThemes();

            if (in_array($name, $allThemes) and $cTheme !== $name) {
                $theme_path = getTemplateDirPath($name, true);
                sureRemoveDir($theme_path, true);
                CacheUtil::clearCache();
                return !is_dir($theme_path);
            }
            break;
    }

    return false;
}

function getAllModules(): array
{
    if (Vars::get("allModules")) {
        return Vars::get("allModules");
    }
    $pkg = new PackageManager();
    $modules = $pkg->getInstalledPackages('modules');
    Vars::set("allModules", $modules);
    return $modules;
}
