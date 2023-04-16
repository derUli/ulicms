<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

use App\Constants\DefaultValues;
use App\Database\DBMigrator;
use App\Helpers\DateTimeHelper;
use App\Helpers\NumberFormatHelper;
use App\Packages\PackageManager;
use App\Packages\SinPackageInstaller;
use App\Services\Connectors\AvailablePackageVersionMatcher;
use App\Utils\CacheUtil;
use App\Utils\File;
use Nette\IOException;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use Robo\Tasks;

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends Tasks
{
    public function __construct()
    {
        if (! defined('CORE_COMPONENT')) {
            define('CORE_COMPONENT', 'robo');
        }

        $this->initUliCMS();

        // If initialization failed, initialize at least ULICMS_ROOT
        // to pass direct access preventions
        if (! defined('ULICMS_ROOT')) {
            define('ULICMS_ROOT', dirname(__FILE__));
        }
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
        Database::truncateTable('history');
    }

    /**
     * truncates the mails database table
     */
    public function truncateMails(): void
    {
        Database::truncateTable('mails');
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

    public function foobar() {

        $skipDirs = [
            'vendor',
            'tests',
            'fm',
            'index.php',
            'CMSConfigSample.php',
            'CMSConfig.php',
            'phpunit_init.php'
        ];

        $protectedFiles = 0;
        $unprotectedFiles = 0;
        foreach (Finder::findFiles(['*.php'])->from('.') as $name => $file) {

            $path = $file->getRealPath();
            $filename = basename($path);

            $skip = false;

            foreach($skipDirs as $skipDir){
                if(str_contains($path, $skipDir)){
                    $skip = true;
                }
            }

            if($skip){
                continue;
            }

            if(str_starts_with($path, '.')){
                continue;
            }

            $output = trim((string)shell_exec("php -f \"{$path}\""));

            if($output === 'No direct script access allowed'){
                $protectedFiles += 1;
            } else {
                $unprotectedFiles += 1;
                $this->writeln("Unprotected: {$path}");
            }
        }

        $totalCheckedFiles = $protectedFiles + $unprotectedFiles;

        $protectedFilesPercent = round(100 / $totalCheckedFiles * $protectedFiles);
        $unprotectedFilesPercent = round(100 / $totalCheckedFiles * $unprotectedFiles);

        $this->writeln("Protected files: {$protectedFiles} ({$protectedFilesPercent}%)");
        $this->writeln("Unprotected files: {$unprotectedFiles} ({$unprotectedFilesPercent}%)");
    }

    /**
     * shows the value of a setting
     * @param string $settingsName settings identifier name
     */
    public function settingsGet($settingsName): void
    {
        $value = Settings::get($settingsName) !== null ?
                Settings::get($settingsName) : DefaultValues::NULL_VALUE;
        $this->writeln($value);
    }

    /**
     * sets the value of a setting
     * @param string $settingsName settings identifier name
     * @param string $value value to set
     */
    public function settingsSet($settingsName, $value): void
    {
        if (strtoupper($value) !== DefaultValues::NULL_VALUE) {
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
        Settings::set('maintenance_mode', '1');
    }

    /**
     * Disables the maintenance mode
     */
    public function maintenanceOff()
    {
        Settings::set('maintenance_mode', '0');
    }

    /**
     * Shows the status of maintenance mode
     */
    public function maintenanceStatus()
    {
        $this->writeln(strbool(is_maintenance_mode()));
    }

    /**
     * examines a *.sin SimpleInstall v2 package file
     * @param string $file path to *.sin package file
     */
    public function packageExamine(string $file)
    {
        if (! is_file($file)) {
            $this->writeln('File ' . basename($file) . ' not found!');
            return;
        }
        $json = json_decode(file_get_contents($file), true);
        ksort($json);

        $this->showPageKeys($json);
    }

    /**
     * list all installed packages
     */
    public function packagesList()
    {
        $this->writeln('Modules:');
        $this->modulesList([]);
        $this->writeln('');
        $this->writeln('Themes:');
        $this->themesList([]);
    }

    /**
     * installs a SimpleInstall v1 or SimpleInstall v2 package
     * @param string $file path to *.sin or *.tar.gz package file
     */
    public function packageInstall($file): void
    {
        if (! is_file($file)) {
            $this->writeln("Can't open {$file}. File doesn't exists.");
            return;
        }

        $result = false;

        if (str_ends_with($file, '.tar.gz')) {
            $pkg = new PackageManager();
            $result = $pkg->installPackage($file);
        } elseif (str_ends_with($file, '.sin')) {
            $pkg = new SinPackageInstaller($file);
            $result = $pkg->installPackage();
        }
        if ($result) {
            $this->writeln('Package ' . basename($file)
                    . ' successfully installed');
            return;
        }
            $this->writeln('Installation of package '
                    . basename($file) . ' failed.');

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
            if (uninstall_module($module, 'module')) {
                $this->writeln("Package {$module} removed.");
            } else {
                $this->writeln("Removing {$module} failed.");
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
            $releases = $data['data'];
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
                $version = getThemeMeta($theme[$i], 'version');
                $line = $theme[$i];
                if ($version !== null) {
                    $line .= " {$version}";
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
            if (uninstall_module($theme, 'theme')) {
                $this->writeln("Package {$theme} removed.");
            } else {
                $this->writeln("Removing {$theme} failed.");
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
        $folder = Path::resolve($directory . '/up');

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
        $folder = Path::resolve($directory . '/down');

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

        $migrator = new DBMigrator($component ?: '[all]', getcwd());
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
                Database::escapeValue($component) . "'" : '1=1';
        $result = Database::query('Select component, name, date from {prefix}dbtrack '
                        . "where {$where} order by component, date", true);
        while ($row = Database::fetchObject($result)) {
            $this->writeln("{$row->component} | {$row->name} | {$row->date}");
        }
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
     * Run PHPUnit Tests
     * @param string $testFile test file to run
     */
    public function testsRun(string $testFile = '')
    {
        $command = 'vendor/bin/phpunit';
        if (DIRECTORY_SEPARATOR === '\\') {
            $command = str_replace('/', '\\', $command);
        }

        system("{$command} {$testFile}");
    }

    /**
     * Run PHPUnit Tests and update snapshots
     * @param string $testFile test file to run
     */
    public function testsUpdateSnapshots(string $testFile = '')
    {
        $command = 'vendor/bin/phpunit -d --update-snapshots';
        if (DIRECTORY_SEPARATOR === '\\') {
            $command = str_replace('/', '\\', $command);
        }

        system("{$command} {$testFile}");
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

    /**
     * Execute cronjobs
     */
    public function cron()
    {
        do_event('before_cron');
        require 'lib/cron.php';
        do_event('after_cron');

        $timezone = DateTimeHelper::getCurrentTimezone();
        $currentLocale = DateTimeHelper::getCurrentLocale();

        $formatter = new IntlDateFormatter($currentLocale, IntlDateFormatter::MEDIUM, IntlDateFormatter::MEDIUM, $timezone);
        $pattern = str_replace(',', '', $formatter->getPattern());
        $formatter->setPattern($pattern);

        $formatedCurrentTime = $formatter->format(time());

        $this->writeln('Finished cron at ' . $formatedCurrentTime);
    }

    /**
     * Prepare build
     */
    public function buildPrepare() {
        $this->buildCopyChangelog();
        $this->buildLicenses();
        $this->buildPhpCsFixer();
    }

    /**
     * Run php-cs-fiyer
     */
    public function buildPhpCsFixer() {
       system('vendor/bin/php-cs-fixer fix');
    }

    /**
     * Copy changelog to core_info module
     */
    public function buildCopyChangelog() {
        FileSystem::copy('../doc/changelog.txt', ULICMS_CONTENT . '/modules/core_info/changelog.txt', true);
    }

    /**
     * Generate license files
     */
    public function buildLicenses()
    {
        system('vendor/bin/php-legal-licenses generate --hide-version');
        system('node_modules/.bin/license-report --only=prod --output=json > licenses.json');
    }

    /**
     * Optimize resources
     */
    public function buildOptimizeResources(){
        $this->buildOptimizeImages();
        $this->buildOptimizeSvg();
    }

    /**
     * Optimize all image files
     */
    public function buildOptimizeImages() {
        $dirs = [];

        foreach(Finder::findFiles(['*.jpg', '*.png'])->from('.') as $name => $file) {
            $dirs[] = dirname($file->getRealPath());
        }

        $dirs = array_unique($dirs);
        $dirs = array_filter($dirs, static function($dir) {
            return ! str_contains($dir, 'fixtures');
        });

        foreach($dirs as $dir){
            $args = [
                '-nr', // Don't recurse through subdirectories.
                '-q 83', // JPEG Quality
                $dir
            ];

            $cmd = 'optimize-images ' . implode(' ', $args);

            $this->writeln($cmd);

            system($cmd);
        }
    }
    
     /**
     * Optimize all svg files
     */
    public function buildOptimizeSvg() {
        $files = [];

        foreach(Finder::findFiles(['*.svg'])->from('.') as $name => $file) {
            $files[] = $file->getRealPath();
        }

        $files = array_unique($files);
        $files = array_filter($files, static function($file) {
            return ! str_contains($file, 'fixtures');
        });

        foreach($files as $file){
            $args = [
                '--multipass',
                $file,
                '-o',
                $file
            ];

            $cmd = 'svgo ' . implode(' ', $args);

            $this->writeln($cmd);

            system($cmd);
        }
    }


    /**
     * Cleanup vendor directory
     */
    public function buildCleanupVendor() {
        $this->cleanUpDirectory('vendor');
    }

    /**
     * Cleanup node_modules directory
     */
    public function buildCleanupNodeModules() {
        $this->cleanUpDirectory('node_modules');
    }

    /**
     * Clean up directory
     *
     * @param string $directory
     *
     * @return void
     */
    protected function cleanUpDirectory(string $directory = 'vendor'): void {
        $patterns = [
            'test',
            'tests',
            'doc',
            'docs',
            '.github',
            '.gitignore',
            '*.bat',
            'CODE_OF_CONDUCT.md',
            'CONTRIBUTING.md',
            'CHANGELOG.md',
            'Contributors.md',
            'phpunit.xml',
            'phpunit.xml.*',
            '.phpunit.*',
            '.php-cs-fixer.*',
            '.travis.yml',
            '.styleci',
            '.coveralls.yml',
            '.gitattributes',
            'README.md',
            '.psalm',
            '.settings',
            '.editorconfig',
            '.project',
            '.stylelintrc',
            '.circleci',
            '.commitlintrc.json',
            '.husky',
            '.vscode'
        ];

        $size = 0;
        $files = 0;
        $filesToDelete = [];

        $searchResult = Finder::find($patterns)->from($directory)->collect();

        foreach($searchResult as $file) {

            $path = $file->getRealPath();
            $files += 1;
            $size += $file->getSize();

            if(! in_array($path, $filesToDelete)){
                $filesToDelete[] = $path;
            }
        }

        foreach($filesToDelete as $file){
            try{
                $this->writeln($file);
                FileSystem::delete($path);
            }
            catch(IOException $e){
                $this->writeln('Errror ' . $path);
            }
        }

        $this->writeln('Files: ' . NumberFormatHelper::formatSizeUnits($size));
        $this->writeln('Size: ' . $files);
    }

    protected function initUliCMS()
    {
        try {
            $this->initCore();
        } catch (Exception $e) {
            $this->showException($e);
        }
    }

    protected function showException(Exception $e)
    {
        $this->writeln($e->getMessage());
    }

    protected function initCore()
    {
        if (! defined('ULICMS_ROOT')) {
            require dirname(__FILE__) . '/init.php';
            require_once getLanguageFilePath('en');
        }
    }

    private function showPageKeys($json)
    {
        $skipAttributes = [
            'data',
            'screenshot'
        ];

        foreach ($json as $key => $value) {
            if (in_array($key, $skipAttributes)) {
                continue;
            }
            if (is_array($value)) {
                $processedValue = implode(', ', $value);
            } else {
                $processedValue = $value;
            }
            $this->writeln("{$key}: {$processedValue}");
        }
    }

    private function getModuleInfo(string $name): string
    {
        $version = getModuleMeta($name, 'version');
        $line = $name;

        if ($version !== null) {
            $line .= " {$version}";
        }
        $module = new Module($name);
        $status = $module->isEnabled() ? 'enabled' : 'disabled';
        $line .= " ({$status})";
        return $line;
    }

    private function replaceModulePlaceholders(array $modules): array
    {
        $manager = new ModuleManager();
        $manager->sync();
        $outModules = [];

        foreach ($modules as $name) {
            if (strtolower($name) == '[all]') {
                $outModules = array_merge(
                    $outModules,
                    $manager->getAllModuleNames()
                );
            } elseif (strtolower($name) == '[core]') {
                $outModules = array_merge(
                    $outModules,
                    $manager->getAllModuleNames('core')
                );
            } elseif (strtolower($name) == '[extend]') {
                $outModules = array_merge($outModules, $manager->getAllModuleNames('extend'));
            } elseif (strtolower($name) == '[pkgsrc]') {
                $outModules = array_merge(
                    $outModules,
                    $manager->getAllModuleNames('pkgsrc')
                );
            } else {
                $outModules[] = $name;
            }
        }
        return $outModules;
    }
}
