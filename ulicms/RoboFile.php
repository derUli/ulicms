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
     * examines a *.sin SimpleInstall v2 package file
     * @param string $file path to *.sin package file
     */
    public function packageExamine(string $file) {
        if (!file_exists($file)) {
            $this->writeln("File " . basename($file) . " not found!");
            return;
        }
        $json = json_decode(file_get_contents($file), true);
        ksort($json);
        $skipAttributes = array(
            "data",
            "screenshot"
        );
        foreach ($json as $key => $value) {
            if (in_array($key, $skipAttributes)) {
                continue;
            }
            if (is_array($value)) {
                $processedValue = implode(", ", $value);
            } else {
                $processedValue = $value;
            }
            $this->writeln("$key: $processedValue");
        }
    }

    /**
     * list all installed packages
     */
    public function packagesList() {
        $this->writeln("Modules:");
        $this->modulesList();
        $this->writeln("");
        $this->writeln("Themes:");
        $this->themesList();
    }

    /**
     * installs a SimpleInstall v1 or SimpleInstall v2 package
     * @param string $file path to *.sin or *.tar.gz package file
     */
    public function packageInstall($file): void {
        if (!is_file($file)) {
            $this->writeln("Can't open $file. File doesn't exists");
            return;
        }

        $result = false;

        if (endsWith($file, ".tar.gz")) {
            $pkg = new PackageManager();
            $result = $pkg->installPackage($file);
        } else if (endsWith($file, ".sin")) {
            $pkg = new SinPackageInstaller($file);
            $result = $pkg->installPackage();
        }
        if ($result) {
            $this->writeln('Package ' . basename($file) . " successfully installed");
            return;
        } else {
            $this->writeln('Installation of package ' . basename($file) . " failed");
        }
        if ($pkg instanceof SinPackageInstaller) {
            foreach ($pkg->getErrors() as $error) {
                $this->writeln($error);
            }
        }
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
     * Run sql migrations
     * @param string $component name of the component
     * @param string $directory path to migrations directory
     * @param string $stop path to migrations directory
     */
    public function dbMigrateUp(string $component, string $directory, ?string $stop = null) {
        $folder = Path::resolve($directory . "/up");

        $migrator = new DBMigrator($component, $folder);
        try {
            Database::setEchoQueries(true);
            $migrator->migrate($stop);
        } catch (Exception $e) {
            $this->writeln($e->getMessage());
        } finally {
            Database::setEchoQueries(false);
        }
    }

    /**
     * Run sql migrations
     * @param string $component name of the component
     * @param string $directory path to migrations directory
     * @param string $stop path to migrations directory
     */
    public function dbMigrateDown(string $component, string $directory, ?string $stop = null) {
        $folder = Path::resolve($directory . "/down");

        $migrator = new DBMigrator($component, $folder);
        try {
            Database::setEchoQueries(true);
            $migrator->rollback($stop);
        } catch (Exception $e) {
            $this->writeln($e->getMessage());
        } finally {
            Database::setEchoQueries(false);
        }
    }

}
