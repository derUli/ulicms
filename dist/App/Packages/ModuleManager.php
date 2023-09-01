<?php

declare(strict_types=1);

namespace App\Packages;

use Database;
use Module;
use Settings;

use function getModuleMeta;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

class ModuleManager {
    /**
     * Get all installed modules
     *
     * @return Module[]
     */
    public function getAllModules(): array {
        $modules = [];
        $sql = 'select name from {prefix}modules';
        $result = Database::query($sql, true);

        while ($row = Database::fetchObject($result)) {
            $modules [] = new \App\Models\Packages\Module($row->name);
        }

        return $modules;
    }

    /**
     * Get names of enabled modules
     *
     * @return string[]
     */
    public function getEnabledModuleNames(): array {
        $modules = [];
        $sql = 'select name from {prefix}modules where enabled = 1';
        $result = Database::query($sql, true);

        while ($row = Database::fetchObject($result)) {
            $modules [] = $row->name;
        }

        return $modules;
    }

    /**
     * Get names of all modules
     *
     * @param string|null $source
     *
     * @return string[]
     */
    public function getAllModuleNames(?string $source = null): array {
        $modules = [];
        $sql = 'select name from {prefix}modules';
        $result = Database::query($sql, true);

        while ($row = Database::fetchObject($result)) {
            if ($source && getModuleMeta($row->name, 'source') != $source) {
                continue;
            }

            $modules [] = $row->name;
        }

        return $modules;
    }

    /**
     * Get dependencies of a module
     *
     * @param string|null $module
     * @param string[] $allDeps
     *
     * @return string[]
     */
    public function getDependencies(
        ?string $module,
        array $allDeps = []
    ): array {
        $dependencies = getModuleMeta($module, 'dependencies');

        if ($dependencies) {
            foreach ($dependencies as $dep) {
                $allDeps [] = $dep;
                $allDeps = array_combine($allDeps, $this->getDependencies($dep, $allDeps));
            }
        }

        $allDeps = array_unique($allDeps);
        return $allDeps;
    }

    /**
     * Get modules depend on a module
     *
     * @param string|null $modules
     * @param string[] $allDeps
     *
     * @return string[]
     */
    public function getDependentModules(
        ?string $module,
        array $allDeps = []
    ): array {
        $allModules = $this->getEnabledModuleNames();

        foreach ($allModules as $mod) {
            $dependencies = getModuleMeta($mod, 'dependencies');
            if ($dependencies && in_array($module, $dependencies)) {
                $allDeps [] = $mod;
                $allDeps = array_combine($allDeps, $this->getDependentModules($mod, $allDeps));
            }
        }

        $allDeps = array_unique($allDeps);
        return $allDeps;
    }

    /**
     * Sync modules from filesystem with modules in database
     *
     * @return void
     */
    public function sync(): void {
        $this->removeDeletedModules();
        $this->addNewModules();

        $this->initModulesDefaultSettings();
    }

    /**
     * Remove modules from database which were removed from directory tree
     *
     * @return void
     */
    protected function removeDeletedModules(): void {
        $realModules = getAllModules();

        $dataBaseModules = $this->getAllModuleNames();

        // Nicht mehr vorhandene Module entfernen
        foreach ($dataBaseModules as $dbModule) {
            if (! in_array($dbModule, $realModules)) {
                $module = new \App\Models\Packages\Module($dbModule);
                $module->delete();
            }
        }
    }

    /**
     * Add new modules to database
     *
     * @return void
     */
    protected function addNewModules(): void {
        $realModules = getAllModules();
        $dataBaseModules = $this->getAllModuleNames();

        $newModules = [];

        // Settings aller aktiven Module auslesen und registrieren
        foreach ($realModules as $realModule) {
            $version = getModuleMeta($realModule, 'version');
            if (in_array($realModule, $dataBaseModules)) {
                $this->updateModuleVersion($version, $realModule);
                continue;
            }
            $module = new \App\Models\Packages\Module();
            $module->setName($realModule);
            $module->setVersion($version);
            $module->save();
            $newModules[] = $module;
            $module->enable();
        }

        // try again to enable modules since the first enable
        // of a module would fail if it dependencies modules are not enabled yet
        foreach ($newModules as $module) {
            $module->enable();
        }
    }

    // modules may define default values for it's settings in it's
    // metadata file
    protected function initModulesDefaultSettings(): void {
        $enabledModules = $this->getEnabledModuleNames();
        foreach ($enabledModules as $module) {
            $settings = getModuleMeta($module, 'settings');
            if (! ($settings && is_array($settings))) {
                continue;
            }
            foreach ($settings as $key => $value) {
                Settings::register($key, $value);
            }
        }
    }

    /**
     * Sync module version in database with filesystem
     *
     * @param string|null $version
     * @param string $realModule
     *
     * @return void
     */
    protected function updateModuleVersion(
        ?string $version,
        string $realModule
    ): void {
        $module = new \App\Models\Packages\Module($realModule);
        if ($module->getVersion() !== $version) {
            $module->setVersion($version);
            $module->save();
        }
    }
}
