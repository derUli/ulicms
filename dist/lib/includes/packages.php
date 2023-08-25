<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

use App\Packages\PackageManager;

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
