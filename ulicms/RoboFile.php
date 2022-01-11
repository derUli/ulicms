<?php

declare(strict_types=1);

use UliCMS\Packages\PatchManager;
use UliCMS\Services\Connectors\eXtend\AvailablePackageVersionMatcher;
use UliCMS\Utils\CacheUtil;
use \Robo\Tasks;
use UliCMS\Exceptions\SqlException;

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends Tasks
{
    public function __construct()
    {
        $this->initUliCMS();
    }

    protected function initUliCMS()
    {
        try {
            $this->initCore();
        } catch (SqlException $e) {
            $this->showException($e);
        }
    }

    protected function showException(Exception $e)
    {
        $this->writeln($e->getMessage());
    }

    protected function initCore()
    {
        require_once dirname(__FILE__) . "/init.php";
        require_once getLanguageFilePath("en");
    }

    /**
     * shows the UliCMS release version
     */
    public function version(): void
    {
        $this->writeln(cms_version());
    }

    /**
     * truncates the history database table
     */
    public function truncateHistory(): void
    {
        Database::truncateTable("history");
    }

    /**
     * truncates the mails database table
     */
    public function truncateMails(): void
    {
        Database::truncateTable("mails");
    }

    /**
     * truncates the mails database table
     */
    public function cacheClear(): void
    {
        CacheUtil::clearCache();
    }

    /**
     * List all settings
     */
    public function settingsList(): void
    {
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
    public function settingsGet($settingsName): void
    {
        $value = Settings::get($settingsName) !== null ?
                Settings::get($settingsName) : "[NULL]";
        $this->writeln($value);
    }

    /**
     * sets the value of a setting
     * @param string $settingsName settings identifier name
     * @param string $value value to set
     */
    public function settingsSet($settingsName, $value): void
    {
        if (strtoupper($value) !== "[NULL]") {
            Settings::set($settingsName, $value);
        } else {
            Settings::delete($settingsName);
        }
    }

    /**
     * Enables the maintenance mode
     */
    public function maintenanceOn()
    {
        Settings::set("maintenance_mode", "1");
    }

    /**
     * Disables the maintenance mode
     */
    public function maintenanceOff()
    {
        Settings::set("maintenance_mode", "0");
    }

    /**
     * Shows the status of maintenance mode
     */
    public function maintenanceStatus()
    {
        $this->writeln(strbool(isMaintenanceMode()));
    }

    /**
     * examines a *.sin SimpleInstall v2 package file
     * @param string $file path to *.sin package file
     */
    public function packageExamine(string $file)
    {
        if (!file_exists($file)) {
            $this->writeln("File " . basename($file) . " not found!");
            return;
        }
        $json = json_decode(file_get_contents($file), true);
        ksort($json);


        $this->showPageKeys($json);
    }

    private function showPageKeys($json)
    {
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
    public function packagesList()
    {
        $this->writeln("Modules:");
        $this->modulesList([]);
        $this->writeln("");
        $this->writeln("Themes:");
        $this->themesList([]);
    }

    /**
     * installs a SimpleInstall v1 or SimpleInstall v2 package
     * @param string $file path to *.sin or *.tar.gz package file
     */
    public function packageInstall($file): void
    {
        if (!is_file($file)) {
            $this->writeln("Can't open $file. File doesn't exists.");
            return;
        }

        $result = false;

        if (endsWith($file, ".tar.gz")) {
            $pkg = new PackageManager();
            $result = $pkg->installPackage($file);
        } elseif (endsWith($file, ".sin")) {
            $pkg = new SinPackageInstaller($file);
            $result = $pkg->installPackage();
        }
        if ($result) {
            $this->writeln('Package ' . basename($file)
                    . " successfully installed");
            return;
        } else {
            $this->writeln('Installation of package '
                    . basename($file) . " failed.");
        }
        if ($pkg instanceof SinPackageInstaller) {
            foreach ($pkg->getErrors() as $error) {
                $this->writeln($error);
            }
        }
    }

    /**
     * List all installed modules and their version numbers
     * @param string $modules one or more modules
     */
    public function modulesList(array $modules)
    {
        $modules = count($modules) ?
                $this->replaceModulePlaceholders($modules) : getAllModules();
        if (count($modules) > 0) {
            $modulesCount = count($modules);
            for ($i = 0; $i < $modulesCount; $i++) {
                $this->writeln($this->getModuleInfo($modules[$i]));
            }
        }
    }

    private function getModuleInfo(string $name): string
    {
        $version = getModuleMeta($name, "version");
        $line = $name;

        if ($version !== null) {
            $line .= " $version";
        }
        $module = new Module($name);
        $status = $module->isEnabled() ? "enabled" : "disabled";
        $line .= " ($status)";
        return $line;
    }

    /**
     * toggles one or more modules
     * @param array $modules one or more modules
     */
    public function modulesToggle(array $modules)
    {
        $modules = $this->replaceModulePlaceholders($modules);

        foreach ($modules as $name) {
            $module = new Module($name);

            $module->toggleEnabled();
            $this->writeln($this->getModuleInfo($name));
        }
    }

    private function replaceModulePlaceholders(array $modules): array
    {
        $manager = new ModuleManager();
        $manager->sync();
        $outModules = [];

        foreach ($modules as $name) {
            if (strtolower($name) == "[all]") {
                $outModules = array_merge(
                    $outModules,
                    $manager->getAllModuleNames()
                );
            } elseif (strtolower($name) == "[core]") {
                $outModules = array_merge(
                    $outModules,
                    $manager->getAllModuleNames("core")
                );
            } elseif (strtolower($name) == "[extend]") {
                $outModules = array_merge($outModules, $manager->getAllModuleNames("extend"));
            } elseif (strtolower($name) == "[pkgsrc]") {
                $outModules = array_merge(
                    $outModules,
                    $manager->getAllModuleNames("pkgsrc")
                );
            } else {
                $outModules[] = $name;
            }
        }
        return $outModules;
    }

    /**
     * enables one or more modules
     * @param array $modules one or more modules
     */
    public function modulesEnable(array $modules)
    {
        $modules = $this->replaceModulePlaceholders($modules);

        foreach ($modules as $name) {
            $module = new Module($name);

            $module->enable();
            $this->writeln($this->getModuleInfo($name));
        }
    }

    /**
     * disables one or more modules
     * @param array $modules one or more modules
     */
    public function modulesDisable(array $modules)
    {
        $modules = $this->replaceModulePlaceholders($modules);

        $manager = new ModuleManager();
        $manager->sync();
        foreach ($modules as $name) {
            $module = new Module($name);
            $module->disable();
            $this->writeln($this->getModuleInfo($name));
        }
    }

    /**
     * Uninstalls one or more modules
     * @param array $modules one or more modules
     */
    public function modulesRemove(array $modules)
    {
        foreach ($modules as $module) {
            if (uninstall_module($module, "module")) {
                $this->writeln("Package $module removed.");
            } else {
                $this->writeln("Removing $module failed.");
            }
        }
    }

    /**
     * get available versions of a module from eXtend
     * @param array $modules one or more modules
     */
    public function modulesGetPackageVersions(array $modules)
    {
        $modules = $this->replaceModulePlaceholders($modules);

        foreach ($modules as $module) {
            $url = "https://extend.ulicms.de/{$module}.json";
            $json = file_get_contents_wrapper($url, true);
            $data = json_decode($json, true);
            $releases = $data["data"];
            $checker = new AvailablePackageVersionMatcher($releases);
            $this->writeln(
                var_dump_str($checker->getCompatibleVersions())
            );
        }
    }

    /**
     * List all installed themes and their version numbers
     */
    public function themesList()
    {
        $theme = getAllThemes();
        if (count($theme) > 0) {
            $themesCount = count($theme);
            for ($i = 0; $i < $themesCount; $i++) {
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
    public function themesRemove(array $themes)
    {
        foreach ($themes as $theme) {
            if (uninstall_module($theme, "theme")) {
                $this->writeln("Package $theme removed.");
            } else {
                $this->writeln("Removing $theme failed.");
            }
        }
    }

    /**
     * Run sql migrations
     * @param string $component name of the component
     * @param string $directory path to migrations directory
     * @param string $stop path to migrations directory
     */
    public function dbmigratorUp(
        string $component,
        string $directory,
        ?string $stop = null
    ): void {
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
    public function dbmigratorDown(
        string $component,
        string $directory,
        ?string $stop = null
    ): void {
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

    /**
     * reset dbtrack table
     * @param string $component name of the component
     */
    public function dbmigratorReset(?string $component = null): void
    {
        Database::setEchoQueries(true);

        $migrator = new DBMigrator($component ? $component : "[all]", getcwd());
        if ($component) {
            $migrator->resetDBTrack();
        } else {
            $migrator->resetDBTrackAll();
        }

        Database::setEchoQueries(false);
    }

    /**
     * list all applied sql migrations
     * @param string $component name of the component
     */
    public function dbmigratorList(?string $component = null): void
    {
        $where = $component ? "component='" .
                Database::escapeValue($component) . "'" : "1=1";
        $result = Database::query("Select * from {prefix}dbtrack "
                        . "where $where order by component, date", true);
        while ($row = Database::fetchObject($result)) {
            $this->writeln("{$row->component} | {$row->name} | {$row->date}");
        }
    }

    /**
     * get a list of all available patches
     */
    public function patchesAvailable()
    {
        $available = $this->patchckAvailable();
        if (!$available) {
            $this->writeln("No patches available");
        }
        $this->writeln(trim($available));
    }

    private function patchckAvailable()
    {
        return file_get_contents_wrapper(PATCH_CHECK_URL, true);
    }

    /**
     * Truncate list of installed patches in database
     */
    public function patchesTruncate(): void
    {
        $patchManager = new PatchManager();
        $patchManager->truncateInstalledPatches();
    }

    /**
     * Sync installed modules with database
     */
    public function modulesSync(): void
    {
        $modules = new ModuleManager();
        $modules->sync();
    }

    /**
     * List installed patches
     */
    public function patchesInstalled()
    {
        $patchManager = new PatchManager();
        $installedPatches = $patchManager->getInstalledPatchNames();
        if (count($installedPatches) == 0) {
            $this->writeln("No Patches installed");
            return;
        }
        foreach ($installedPatches as $patch) {
            $this->writeln($patch);
        }
    }

    /**
     * install patches
     * @param array $patchesToInstall name of the patches to install or "all"
     */
    public function patchesInstall(array $patchesToInstall): void
    {
        $patchManager = new PatchManager();
        $available = $this->patchckAvailable();
        if (!$available) {
            $this->writeln("no patches available");
            return;
        }
        $availablePatches = $patchManager->getAvailablePatches();

        $filteredPatches = [];
        foreach ($availablePatches as $patch) {
            if (faster_in_array($patch->name, $patchesToInstall) ||
                    faster_in_array("all", $patchesToInstall)) {
                $filteredPatches[] = $patch;
            }
        }
        // apply patches
        foreach ($filteredPatches as $patch) {
            $this->writeln("Apply patch {$patch->name}...");
            if ($patch->install()) {
                $this->writeln("Patch {$patch->name} applied");
            } else {
                $this->writeln("Installation of patch {$patch->name} failed.");
                exit(1);
            }
        }
    }

    /**
     * Run PHPUnit Tests
     * @param string $testFile test file to run
     */
    public function testsRun(string $testFile = "")
    {
        $command = "vendor/bin/phpunit";
        if (DIRSEP === "\\") {
            $command = str_replace("/", "\\", $command);
        }

        system("$command $testFile");
    }
    
    /**
     * Run PHPUnit Tests and update snapshots
     * @param string $testFile test file to run
     */
    public function testsUpdateSnapshots(string $testFile = "")
    {
        $command = "vendor/bin/phpunit -d --update-snapshots";
        if (DIRSEP === "\\") {
            $command = str_replace("/", "\\", $command);
        }

        system("$command $testFile");
    }
    
    /**
     * Creates the application's database
     */
    public function dbCreate()
    {
        Database::setEchoQueries(true);
        $cfg = new CMSConfig();
        Database::createSchema($cfg->db_database);
        Database::select($cfg->db_database);
    }

    /**
     * Drop and recreate the application's database
     */
    public function dbMigrate()
    {
        Database::setEchoQueries(true);

        $cfg = new CMSConfig();
        $additionalSql = is_array($cfg->dbmigrator_initial_sql_files) ?
                $cfg->dbmigrator_initial_sql_files : [];

        Database::setupSchemaAndSelect(
            $cfg->db_database,
            $additionalSql
        );
    }

    /**
     * Drops the application's database
     */
    public function dbDrop()
    {
        $cfg = new CMSConfig();
        Database::setEchoQueries(true);
        if (Database::isConnected()) {
            Database::dropSchema($cfg->db_database);
        }
    }

    /**
     * Creates and migrates the application's database
     */
    public function dbReset()
    {
        Database::setEchoQueries(true);

        $this->dbDrop();

        $this->dbMigrate();
    }
}
