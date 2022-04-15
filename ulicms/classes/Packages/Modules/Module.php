<?php

declare(strict_types=1);

namespace UliCMS\Packages\Modules;

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use Database;
use ModuleHelper;
use UliCMS\Packages\Modules\ModuleManager;
use function getModuleMeta;
use function getModuleAdminFilePath;
use function getModuleUninstallScriptPath;
use function getModuleUninstallScriptPath2;
use function uninstall_module;

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
        $sql = "select * from {prefix}modules where name = ?";
        $args = array(
            $name
        );
        $result = Database::pQuery($sql, $args, true);
        $dataset = Database::fetchSingle($result);

        if ($dataset) {
            $this->name = $dataset->name;
            $this->version = $dataset->version;
            $this->enabled = boolval($dataset->enabled);
            return true;
        }
        return false;
    }

    public function save(): void {
        $sql = "select name from {prefix}modules where name = ?";
        $args = array(
            $this->name
        );
        $result = Database::pQuery($sql, $args, true);

        if (Database::any($result)) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    protected function insert(): bool {
        $sql = "INSERT INTO {prefix}modules (name, version, enabled) "
                . "values(?, ?, ?)";
        $args = array(
            $this->name,
            $this->version,
            $this->enabled
        );
        return Database::pQuery($sql, $args, true);
    }

    protected function update(): bool {
        $sql = "update {prefix}modules set version = ?, enabled = ? "
                . "where name = ?";
        $args = array(
            $this->version,
            $this->enabled,
            $this->name
        );
        return Database::pQuery($sql, $args, true);
    }

    public function getVersion(): ?string {
        return $this->version;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function isEnabled(): bool {
        return boolval($this->enabled);
    }

    public function enable(): void {
        if (!$this->isMissingDependencies()) {
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
            if (!in_array($dependency, $enabledMods)) {
                $result [] = $dependency;
            }
        }
        return $result;
    }

    public function isInstalled(): bool {
        if (!$this->getName()) {
            return false;
        }
        return !is_null(getModuleMeta($this->getName()));
    }

    public function isMissingDependencies(): bool {
        return (count($this->getMissingDependencies()) > 0);
    }

    public function hasAdminPage(): bool {
        $controller = ModuleHelper::getMainController($this->name);
        return (file_exists(getModuleAdminFilePath($this->name))
                or file_exists(getModuleAdminFilePath2($this->name))
                or ($controller and method_exists($controller, "settings"))
                or (getModuleMeta($this->name, "main_class")) and
                getModuleMeta($this->name, "admin_permission"));
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
        return (count($this->getDependentModules()) > 0);
    }

    public function disable(): void {
        if (!$this->hasDependentModules()) {
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
        $this->name = strval($name);
    }

    public function setVersion(?string $version): void {
        $this->version = $version ? strval($version) : null;
    }

    public function hasUninstallEvent(): bool {
        $name = $this->name;
        $uninstallScript1 = getModuleUninstallScriptPath($name, true);
        $uninstallScript2 = getModuleUninstallScriptPath2($name, true);

        // Uninstall Script ausführen, sofern vorhanden
        $mainController = ModuleHelper::getMainController($name);
        return (($mainController &&
                method_exists($mainController, "uninstall")) ||
                file_exists($uninstallScript1) ||
                file_exists($uninstallScript2)
                );
    }

    public function delete(): ?bool {
        $sql = "select name from {prefix}modules where name = ?";
        $args = array(
            $this->name
        );
        $result = Database::pQuery($sql, $args, true);
        if (Database::any($result)) {
            $sql = "delete from {prefix}modules where name = ?";
            $args = array(
                $this->name
            );
            return Database::pQuery($sql, $args, true);
        }
        return null;
    }

    public function uninstall(): bool {
        return uninstall_module($this->getName(), "module");
    }

}
