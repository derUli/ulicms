<?php

declare(strict_types=1);

class ModuleManager {

    public function getAllModules(): array {
        $modules = [];
        $sql = "select name from {prefix}modules";
        $query = Database::query($sql, true);
        while ($row = Database::fetchObject($query)) {
            $modules [] = new Module($row->name);
        }
        return $modules;
    }

    public function getEnabledModuleNames(): array {
        $modules = [];
        $sql = "select name from {prefix}modules where enabled = 1";
        $query = Database::query($sql, true);
        while ($row = Database::fetchObject($query)) {
            $modules [] = $row->name;
        }
        return $modules;
    }

    public function getDisabledModuleNames(): array {
        $modules = [];
        $sql = "select name from {prefix}modules where enabled = 0";
        $query = Database::query($sql, true);
        while ($row = Database::fetchObject($query)) {
            $modules [] = $row->name;
        }
        return $modules;
    }

    public function getAllModuleNames(): array {
        $modules = [];
        $sql = "select name from {prefix}modules";
        $query = Database::query($sql, true);
        while ($row = Database::fetchObject($query)) {
            $modules [] = $row->name;
        }
        return $modules;
    }

    public function getDependencies(?string $module, array $allDeps = []): array {
        $dependencies = getModuleMeta($module, "dependencies");
        if ($dependencies) {
            foreach ($dependencies as $dep) {
                $allDeps [] = $dep;
                $allDeps = array_combine($allDeps, $this->getDependencies($dep, $allDeps));
            }
        }
        $allDeps = array_unique($allDeps);
        return $allDeps;
    }

    public function getDependentModules(?string $module, array $allDeps = []): array {
        $allModules = $this->getEnabledModuleNames();
        foreach ($allModules as $mod) {
            $dependencies = getModuleMeta($mod, "dependencies");
            if ($dependencies && faster_in_array($module, $dependencies)) {
                $allDeps [] = $mod;
                $allDeps = array_combine($allDeps, $this->getDependentModules($mod, $allDeps));
            }
        }
        $allDeps = array_unique($allDeps);
        return $allDeps;
    }

    // Diese Funktion synchronisiert die modules in der Datenbank mit den modules im Modulordner
    // - Neue Module werden erfassen
    // - Versionsupdates erfassen
    // - Nicht mehr vorhandene Module aus Datenbank löschen
    // - neue Module sollen erst mal deaktiviert sein
    // - Diese Funktion aufrufen beim installieren von Modulen, beim leeren des Caches und beim deinstallieren von Modulen
    public function sync(): void {
        $this->removeDeletedModules();
        $this->addNewModules();
    }

    protected function removeDeletedModules() {
        $realModules = getAllModules();

        $dataBaseModules = $this->getAllModuleNames();
        // Nicht mehr vorhandene Module entfernen
        foreach ($dataBaseModules as $dbModule) {
            if (!faster_in_array($dbModule, $realModules)) {
                $module = new Module($dbModule);
                $module->delete();
            }
        }
    }

    protected function addNewModules() {
        $realModules = getAllModules();
        $dataBaseModules = $this->getAllModuleNames();
        // Settings aller aktiven Module auslesen und registrieren
        foreach ($realModules as $realModule) {
            $version = getModuleMeta($realModule, "version");
            if (faster_in_array($realModule, $dataBaseModules)) {
                $this->updateModuleVersion($version, $realModule);
                continue;
            }
            $module = new Module ();
            $module->setName($realModule);
            $module->setVersion($version);
            $module->save();
            $module->enable();
        }
    }

    protected function initModulesDefaultSettings(): void {
        $enabledModules = $this->getEnabledModuleNames();
        foreach ($enabledModules as $module) {
            $settings = getModuleMeta($module, "settings");
            if (!($settings and is_array($settings))) {
                continue;
            }
            foreach ($settings as $key => $value) {
                Settings::register($key, $value);
            }
        }
    }

    protected function updateModuleVersion(string $version, string $realModule): void {

        $module = new Module($realModule);
        if ($module->getVersion() !== $version) {
            $module->setVersion($version);
        }
        $module->save();
    }

}
