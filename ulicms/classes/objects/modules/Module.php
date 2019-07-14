<?php

declare(strict_types=1);

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
        $query = Database::pQuery($sql, $args, true);
        $dataset = Database::fetchSingle($query);

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
        $query = Database::pQuery($sql, $args, true);

        if (Database::any($query)) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    protected function insert(): bool {
        $sql = "INSERT INTO {prefix}modules (name, version, enabled) values(?, ?, ?)";
        $args = array(
            $this->name,
            $this->version,
            $this->enabled
        );
        return Database::pQuery($sql, $args, true);
    }

    protected function update(): bool {
        $sql = "update {prefix}modules set version = ?, enabled = ? where name = ?";
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
        $manager = new ModuleManager ();
        $dependencies = $manager->getDependencies($this->name);
        $enabledMods = $manager->getEnabledModuleNames();
        foreach ($dependencies as $dependency) {
            if (!faster_in_array($dependency, $enabledMods)) {
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
                or ( $controller and method_exists($controller, "settings"))
                or ( getModuleMeta($this->name, "main_class")) and
                getModuleMeta($this->name, "admin_permission"));
    }

    public function isEmbedModule(): bool {
        return ModuleHelper::isEmbedModule($this->name);
    }

    public function getDependentModules(): array {
        $result = [];
        $manager = new ModuleManager ();
        $enabledMods = $manager->getEnabledModuleNames();
        $dependent = $manager->getDependentModules($module);

        foreach ($dependent as $dep) {
            if (faster_in_array($dep, $enabledMods)) {
                $result [] = $dependency;
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
        if ($version === null) {
            $this->version = null;
        }
        $this->version = strval($version);
    }

    public function delete(): ?bool {
        $sql = "select name from {prefix}modules where name = ?";
        $args = array(
            $this->name
        );
        $query = Database::pQuery($sql, $args, true);
        if (Database::any($query)) {
            $sql = "delete from {prefix}modules where name = ?";
            $args = array(
                $this->name
            );
            return Database::pQuery($sql, $args, true);
        }
        return null;
    }

}
