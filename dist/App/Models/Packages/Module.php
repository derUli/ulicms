<?php

declare(strict_types=1);

namespace App\Models\Packages;

use App\Controllers\Controller;
use App\Packages\ModuleManager;
use ControllerRegistry;
use Database;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

class Module {
    private ?string $name = null;

    private ?string $version = null;

    private bool $enabled = false;

    /**
     * Constructor
     *
     * @param ?string $name
     */
    public function __construct(?string $name = null) {
        if ($name) {
            $this->name = $name;
            $this->loadByName($name);
        }
    }

    /**
     * Load module from database
     *
     * @param string $name
     */
    public function loadByName(string $name): bool {
        $sql = 'select * from {prefix}modules where name = ?';
        $args = [
            $name
        ];
        $result = Database::pQuery($sql, $args, true);

        /** @var object{name: string, version: string, enabled: bool}|null */
        $dataset = Database::fetchSingle($result);

        if ($dataset) {
            $this->name = $dataset->name;
            $this->version = $dataset->version;
            $this->enabled = (bool)$dataset->enabled;
            return true;
        }
        return false;
    }

    /**
     * Save module in database
     *
     * @return void
     */
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

    /**
     * Get version number
     *
     * @return string|null
     */
    public function getVersion(): ?string {
        return $this->version;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * Get missing dependencies
     * @return string[]
     */
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

    public function isMissingDependencies(): bool {
        return count($this->getMissingDependencies()) > 0;
    }

    /**
     * Returns metadata from a module
     *
     * @param ?string $attrib return a specific attribute
     *
     * @return mixed array from Json file or the value of the given $attrib
     */
    public function getMeta(?string $attrib = null): mixed {
        $metadata_file = \App\Helpers\ModuleHelper::buildModuleRessourcePath(
            $this->name ?? '',
            'metadata.json',
            true
        );

        if (! is_file($metadata_file)) {
            return null;
        }

        $data = (string)file_get_contents($metadata_file);
        $json = json_decode($data, true);

        if ($attrib && ! isset($json[$attrib])) {
            return null;
        }

        return $attrib ? $json[$attrib] : $json;
    }

    /**
     * Check if the module has a settings page
     *
     * @return bool
     */
    public function hasAdminPage(): bool {
        $controller = $this->getMainController();
        return
            is_file(getModuleAdminFilePath($this->name)) ||
            ($controller && method_exists($controller, 'settings')) ||
            (
                $this->getMeta('main_class')
            ) &&
            $this->getMeta('admin_permission');
    }

    /**
     * Check if the module is an embed module
     *
     * @return bool
     */
    public function isEmbedModule(): bool {
        return (bool)$this->getMeta('embed');
    }

    /**
     * Check if the module is installed
     *
     * @return bool
     */
    public function isInstalled(): bool {
        return $this->getMeta() !== null;
    }

    /**
     * Get embed shortcode
     *
     * @return ?string
     */
    public function getShortCode(): ?string {
        return $this->getName() ? "[module={$this->getName()}]" : null;
    }

    /**
     * Check if module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool {
        return (bool)$this->enabled;
    }

    /**
     * Enable module
     *
     * @return void
     */
    public function enable(): void {
        if (! $this->isMissingDependencies()) {
            $this->enabled = true;
            $this->save();
        }
    }

    /**
     * Disable module
     *
     * @return void
     */
    public function disable(): void {
        if (! $this->hasDependentModules()) {
            $this->enabled = false;
            $this->save();
        }
    }

    /**
     * Toggle module
     *
     * @return void
     */
    public function toggleEnabled(): void {
        if ($this->isEnabled()) {
            $this->disable();
        } else {
            $this->enable();
        }
    }

    /**
     * Set name
     *
     * @param ?string $name
     *
     * @return void
     */
    public function setName(?string $name): void {
        $this->name = $name;
    }

    /**
     * Set version
     *
     * @param ?string $version
     *
     * @return void
     */
    public function setVersion(?string $version): void {
        $this->version = $version;
    }

    /**
     * Get dependent modules
     *
     * @return string[]
     */
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

    /**
     * Check if the module has dependent modules
     *
     * @return bool
     */
    public function hasDependentModules(): bool {
        return count($this->getDependentModules()) > 0;
    }

    /**
     * Check if the module has an uninstall event
     *
     * @return bool
     */
    public function hasUninstallEvent(): bool {
        $name = $this->name;
        // Uninstall Script ausführen, sofern vorhanden
        $mainController = $this->getMainController();
        return $mainController && method_exists($mainController, 'uninstall');
    }

    /**
     * Delete module from database
     *
     * @return ?bool
     */
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

    /**
     * Uninstall module
     *
     * @return bool
     */
    public function uninstall(): bool {
        return uninstall_module((string)$this->getName(), 'module');
    }

    // returns an instance of the MainClass of a module
    public function getMainController(): ?Controller {
        $controller = null;

        /**
         * @var string
         */
        $mainClass = $this->getMeta('main_class');

        if ($mainClass) {
            $controller = ControllerRegistry::get($mainClass);
        }

        return $controller;
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
