<?php

declare(strict_types=1);

use UliCMS\Utils\CacheUtil;

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks {

    public function __construct() {
        require_once dirname(__FILE__) . "/init.php";
    }

    /**
     * shows the UliCMS release version
     */
    public function version(): void {
        $this->writeln(cms_version());
    }

    /**
     * truncates the history database table
     */
    public function truncateHistory(): void {
        Database::truncateTable("history");
    }

    /**
     * truncates the mails database table
     */
    public function truncateMails(): void {
        Database::truncateTable("mails");
    }

    /**
     * truncates the mails database table
     */
    public function cacheClear(): void {
        CacheUtil::clearCache();
    }

    /**
     * List all settings
     */
    public function settingsList(): void {
        // show all settings
        $settings = Settings::getAll();
        foreach ($settings as $setting) {
            if (empty($setting->name)) {
                continue;
            }
            $this->writeln("{$setting->name}: {$setting->value}");
        }
    }

    /**
     * shows the value of a setting
     * @param string $settingsName settings identifier name
     */
    public function settingsGet($settingsName): void {
        $value = Settings::get($settingsName) !== null ?
                Settings::get($settingsName) : "[NULL]";
        $this->writeln($value);
    }

    /**
     * sets the value of a setting
     * @param string $settingsName settings identifier name
     * @param string $value value to set
     */
    public function settingsSet($settingsName, $value): void {
        if (strtoupper($value) !== "[NULL]") {
            Settings::set($settingsName, $value);
        } else {
            Settings::delete($settingsName);
        }
    }

    /**
     * Enables the maintenance mode
     */
    public function maintenanceOn() {
        Settings::set("maintenance_mode", "1");
    }

    /**
     * Disables the maintenance mode
     */
    public function maintenanceOff() {
        Settings::set("maintenance_mode", "0");
    }

    /**
     * Shows the status of maintenance mode
     */
    public function maintenanceStatus() {
        $this->writeln(strbool(isMaintenanceMode()));
    }

    /**
     * List all installed modules and their version numbers
     */
    public function modulesList() {
        $modules = getAllModules();
        if (count($modules) > 0) {
            for ($i = 0; $i < count($modules); $i ++) {
                $version = getModuleMeta($modules[$i], "version");
                $line = $modules[$i];
                if ($version !== null) {
                    $line .= " $version";
                }
                $this->writeln($line);
            }
        }
    }

    /**
     * Uninstalls one or more modules
     * @param array $modules one or more modules
     */
    public function modulesRemove(array $modules) {
        foreach ($modules as $module) {
            if (uninstall_module($module, "module")) {
                echo "Package $module removed\n";
            } else {
                echo "Removing  $module failed.\n";
            }
        }
    }

    /**
     * List all installed themes and their version numbers
     */
    public function themesList() {
        $theme = getAllThemes();
        if (count($theme) > 0) {
            for ($i = 0; $i < count($theme); $i ++) {
                $version = getThemeMeta($theme[$i], "version");
                $line = $theme[$i];
                if ($version !== null) {
                    $line .= " $version";
                }
                $this->writeln($line);
            }
        }
    }

    /**
     * Uninstalls one or more themes
     * @param array $themes one or more themes
     */
    public function themesRemove(array $themes) {
        foreach ($themes as $theme) {
            if (uninstall_module($theme, "theme")) {
                echo "Package $theme removed\n";
            } else {
                echo "Removing  $theme failed.\n";
            }
        }
    }

    /**
     * Run core database migrations
     */
    public function dbMigrate() {
        // Run SQL Migration Scripts
        Database::setEchoQueries(true);
        $migrator = new DBMigrator("core", "lib/migrations/up");
        $migrator->migrate();
        Database::setEchoQueries(false);
    }

}
