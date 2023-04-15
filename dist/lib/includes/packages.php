<?php

declare(strict_types=1);

use App\Packages\PackageManager;
use App\Security\PermissionChecker;
use App\Utils\CacheUtil;

/**
 * Get list of all installed modules
 * @return array
 */
function getAllModules(): array
{
    // Check if cached
    if (\App\Storages\Vars::get('allModules')) {
        return \App\Storages\Vars::get('allModules');
    }

    // Fetch installed modules
    $pkg = new PackageManager();
    $modules = $pkg->getInstalledPackages('modules');

    // Save installed modules in cache
    \App\Storages\Vars::set('allModules', $modules);
    return $modules;
}

/**
 * Get list of installed themes
 * @return array
 */
function getAllThemes(): array
{
    $pkg = new PackageManager();
    return $pkg->getInstalledPackages('themes');
}

/**
 * Uninstall a module
 * TODO: Move to PackageManager class
 * @param string $name
 * @param string $type
 * @return bool
 */
function uninstall_module(string $name, string $type = 'module'): bool
{
    $acl = new PermissionChecker(get_user_id());

    if (! $acl->hasPermission('install_packages') && ! is_cli()) {
        return false;
    }

    $name = trim(basename(trim($name)));

    // Verhindern, dass der Modulordner oder gar das ganze
    // CMS gelöscht werden kann
    if ($name == '.' || $name == '..' || empty($name)) {
        return false;
    }
    switch ($type) {
        case 'module':
            $moduleDir = getModulePath($name, true);

            // Modul-Ordner entfernen
            if (is_dir($moduleDir)) {
                $uninstall_script = getModuleUninstallScriptPath($name, true);
                $uninstall_script2 = getModuleUninstallScriptPath2($name, true);

                // Uninstall Script ausführen, sofern vorhanden
                $mainController = ModuleHelper::getMainController($name);
                if ($mainController
                        && method_exists($mainController, 'uninstall')) {
                    $mainController->uninstall();
                } elseif (is_file($uninstall_script)) {
                    require $uninstall_script;
                } elseif (is_file($uninstall_script2)) {
                    require $uninstall_script2;
                }
                sureRemoveDir($moduleDir, true);
                CacheUtil::clearCache();
                return ! is_dir($moduleDir);
            }
            break;
        case 'theme':
            $cTheme = Settings::get('theme');
            $allThemes = getAllThemes();

            if (in_array($name, $allThemes) && $cTheme !== $name) {
                $theme_path = getTemplateDirPath($name, true);
                sureRemoveDir($theme_path, true);
                CacheUtil::clearCache();
                return ! is_dir($theme_path);
            }
            break;
    }

    return false;
}
