<?php

declare(strict_types=1);

namespace App\Models\Packages;

use App\Helpers\ModuleHelper;
use App\Packages\ModuleManager;
use Database;

use function getModuleMeta;
use function getModuleUninstallScriptPath;
use function getModuleUninstallScriptPath2;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

class Module {
    private $name = null;

    private $version = null;

    private $enabled = 0;

    public function __construct(?string $name = null) {
        if ($name) {
            $this->loadByName($name);
        }
    }

    public function loadByName(string $name): bool {
        $sql = 'select * from {prefix}modules where name = ?';
        $args = [
            $name
        ];
        $result = Database::pQuery($sql, $args, true);
        $dataset = Database::fetchSingle($result);

        if ($dataset) {
            $this->name = $dataset->name;
            $this->version = $dataset->version;
            $this->enabled = (bool)$dataset->enabled;
            return true;
        }
        return false;
    }

    public function save(): void {
        $sql = 'select name from {prefix}modules where name = ?';
        $args = [
            $this->name
        ];
        $result = Database::pQuery($sql, $args, true);

        if (Database::any($result)) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    public function getVersion(): ?string {
        return $this->version;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function isEnabled(): bool {
        return (bool)$this->enabled;
    }

    public function enable(): void {
        if (! $this->isMissingDependencies()) {
            $this->enabled = 1;
            $this->save();
        }
    }

    public function getMissingDependencies(): array {
        $result = [];
        $manager = new ModuleManager();
        $dependencies = $manager->getDependencies($this->name);
        $enabledMods = $manager->getEnabledModuleNames();
        foreach ($dependencies as $dependency) {
            if (! in_array($dependency, $enabledMods)) {
                $result [] = $dependency;
            }
        }
        return $result;
    }

    public function isInstalled(): bool {
        if (! $this->getName()) {
            return false;
        }
        return getModuleMeta($this->getName()) !== null;
    }

    public function isMissingDependencies(): bool {
        return count($this->getMissingDependencies()) > 0;
    }

    public function hasAdminPage(): bool {
        $controller = ModuleHelper::getMainController($this->name);
        return
            is_file(getModuleAdminFilePath($this->name)) ||
            ($controller && method_exists($controller, 'settings')) ||
            (
                getModuleMeta($this->name, 'main_class')
            ) &&
            getModuleMeta($this->name, 'admin_permission');
    }

    public function isEmbedModule(): bool {
        return ModuleHelper::isEmbedModule($this->name);
    }

    public function getShortCode(): ?string {
        return $this->getName() ? "[module={$this->getName()}]" : null;
    }

    public function getDependentModules(): array {
        $result = [];
        $manager = new ModuleManager();
        $enabledMods = $manager->getEnabledModuleNames();
        $dependent = $manager->getDependentModules($this->getName());

        foreach ($dependent as $dep) {
            if (in_array($dep, $enabledMods)) {
                $result [] = $dep;
            }
        }
        return $result;
    }

    public function hasDependentModules(): bool {
        return count($this->getDependentModules()) > 0;
    }

    public function disable(): void {
        if (! $this->hasDependentModules()) {
            $this->enabled = 0;
            $this->save();
        }
    }

    public function toggleEnabled(): void {
        if ($this->isEnabled()) {
            $this->disable();
        } else {
            $this->enable();
        }
    }

    public function setName(?string $name): void {
        $this->name = $name;
    }

    public function setVersion(?string $version): void {
        $this->version = $version;
    }

    public function hasUninstallEvent(): bool {
        $name = $this->name;
        $uninstallScript1 = getModuleUninstallScriptPath($name, true);
        $uninstallScript2 = getModuleUninstallScriptPath2($name, true);

        // Uninstall Script ausführen, sofern vorhanden
        $mainController = ModuleHelper::getMainController($name);
        return ($mainController &&
                method_exists($mainController, 'uninstall')) ||
                is_file($uninstallScript1) ||
                is_file($uninstallScript2);
    }

    public function delete(): ?bool {
        $sql = 'select name from {prefix}modules where name = ?';
        $args = [
            $this->name
        ];
        $result = Database::pQuery($sql, $args, true);
        if (Database::any($result)) {
            $sql = 'delete from {prefix}modules where name = ?';
            $args = [
                $this->name
            ];
            return Database::pQuery($sql, $args, true);
        }
        return null;
    }

    public function uninstall(): bool {
        return uninstall_module($this->getName(), 'module');
    }

    protected function insert(): bool {
        $sql = 'INSERT INTO {prefix}modules (name, version, enabled) '
                . 'values(?, ?, ?)';
        $args = [
            $this->name,
            $this->version,
            $this->enabled
        ];
        return Database::pQuery($sql, $args, true);
    }

    protected function update(): bool {
        $sql = 'update {prefix}modules set version = ?, enabled = ? '
                . 'where name = ?';
        $args = [
            $this->version,
            $this->enabled,
            $this->name
        ];
        return Database::pQuery($sql, $args, true);
    }
}
