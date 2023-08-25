<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

use App\Models\Packages\Module;
use App\Models\Packages\Theme;
use App\Packages\PackageManager;
use App\Security\Permissions\PermissionChecker;

/**
 * Get list of all installed modules
 * @return array
 */
function getAllModules(): array {
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
function getAllThemes(): array {
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
function uninstall_module(string $name, string $type = 'module'): bool {
    $acl = new PermissionChecker(get_user_id());

    if (! $acl->hasPermission('install_packages') && ! is_cli()) {
        return false;
    }

    $name = trim(basename($name));

    switch ($type) {
        case 'module':
            $module = new Module($name);
            return $module->uninstall();

        case 'theme':
            $theme = new Theme($name);
            return $theme->uninstall();
    }

    return false;
}
