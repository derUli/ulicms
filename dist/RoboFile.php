<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

use App\Constants\DefaultValues;
use App\Database\DBMigrator;
use App\Helpers\DateTimeHelper;
use App\Packages\PackageManager;
use App\Packages\SinPackageInstaller;
use App\Services\Connectors\AvailablePackageVersionMatcher;
use App\Storages\Settings\ConfigurationToDotEnvConverter;
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
    /**
     * Constructor
     */
    public function __construct()
    {
        if (! defined('CORE_COMPONENT')) {
            define('CORE_COMPONENT', 'robo');
        }
    }

    /**
     * Show the UliCMS release version
     */
    public function version(): void
    {
        $this->writeln(cms_version());
    }

    /**
     * Show the current environment
     */
    public function environment(): void
    {
        $this->writeln(get_environment());
    }

    /**
     * Show the umask
     */
    public function umask(): void
    {
        $this->initUliCMS();
        $umask = str_pad(
            decoct(umask()),
            4,
            '0',
            STR_PAD_LEFT
        );
        $this->writeln($umask);
    }

    /**
     * Truncate the history database table
     */
    public function truncateHistory(): void
    {
        $this->initUliCMS();

        Database::truncateTable('history');
    }

    /**
     * Truncate the mails database table
     */
    public function truncateMails(): void
    {
        $this->initUliCMS();
        Database::truncateTable('mails');
    }

    /**
     * Truncate the mails database table
     */
    public function cacheClear(): void
    {
        $this->initUliCMS();
        CacheUtil::clearCache();
    }

    /**
     * List all settings
     */
    public function settingsList(): void
    {
        $this->initUliCMS();

        // show all settings
        $settings = Settings::getAll();
        foreach ($settings as $setting) {
            $this->writeln("{$setting->name}: {$setting->value}");
        }
    }

    /**
     * Show the value of a setting
     *
     * @param string $settingsName settings identifier name
     */
    public function settingsGet($settingsName): void
    {
        $this->initUliCMS();

        $value = Settings::get($settingsName) !== null ?
                Settings::get($settingsName) : DefaultValues::NULL_VALUE;
        $this->writeln($value);
    }

    /**
     * Set the value of a setting
     *
     * @param string $settingsName settings identifier name
     * @param string $value value to set
     */
    public function settingsSet($settingsName, $value): void
    {
        $this->initUliCMS();

        if (strtoupper($value) !== DefaultValues::NULL_VALUE) {
            Settings::set($settingsName, $value);
        } else {
            Settings::delete($settingsName);
        }
    }

    /**
     * Enable the maintenance mode
     */
    public function maintenanceOn(): void
    {
        $this->initUliCMS();
        Settings::set('maintenance_mode', '1');
    }

    /**
     * Disable the maintenance mode
     */
    public function maintenanceOff(): void
    {
        $this->initUliCMS();
        Settings::set('maintenance_mode', '0');
    }

    /**
     * Show the status of maintenance mode
     */
    public function maintenanceStatus(): void
    {
        $this->initUliCMS();

        $this->writeln(strbool(is_maintenance_mode()));
    }

    /**
     * Examine a *.sin SimpleInstall v2 package file
     *
     * @param string $file path to *.sin package file
     */
    public function packageExamine(string $file): void
    {
        $this->initUliCMS();

        if (! is_file($file)) {
            $this->writeln('File ' . basename($file) . ' not found!');
            return;
        }
        $json = json_decode(file_get_contents($file), true);
        ksort($json);

        $this->showPageKeys($json);
    }

    /**
     * List all installed packages
     */
    public function packagesList(): void
    {
        $this->initUliCMS();

        $this->writeln('Modules:');
        $this->modulesList([]);
        $this->writeln('');
        $this->writeln('Themes:');
        $this->themesList([]);
    }

    /**
     * Install a SimpleInstall v1 or SimpleInstall v2 package
     *
     * @param string $file path to *.sin or *.tar.gz package file
     */
    public function packageInstall($file): void
    {
        $this->initUliCMS();

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
     *
     * @param string $modules one or more modules
     */
    public function modulesList(array $modules): void
    {
        $this->initUliCMS();

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
     * Toggle one or more modules
     * @param array $modules one or more modules
     */
    public function modulesToggle(array $modules): void
    {
        $this->initUliCMS();

        $modules = $this->replaceModulePlaceholders($modules);

        foreach ($modules as $name) {
            $module = new Module($name);

            $module->toggleEnabled();
            $this->writeln($this->getModuleInfo($name));
        }
    }

    /**
     * Enables one or more modules
     *
     * @param array $modules one or more modules
     */
    public function modulesEnable(array $modules): void
    {
        $this->initUliCMS();

        $modules = $this->replaceModulePlaceholders($modules);

        foreach ($modules as $name) {
            $module = new Module($name);

            $module->enable();
            $this->writeln($this->getModuleInfo($name));
        }
    }

    /**
     * Disable one or more modules
     *
     * @param array $modules one or more modules
     */
    public function modulesDisable(array $modules): void
    {
        $this->initUliCMS();

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
     * Uninstall one or more modules
     *
     * @param array $modules one or more modules
     */
    public function modulesRemove(array $modules): void
    {
        $this->initUliCMS();

        foreach ($modules as $module) {
            if (uninstall_module($module, 'module')) {
                $this->writeln("Package {$module} removed.");
            } else {
                $this->writeln("Removing {$module} failed.");
            }
        }
    }

    /**
     * Get available versions of a module from eXtend
     *
     * @param array $modules one or more modules
     */
    public function modulesGetPackageVersions(array $modules): void
    {
        $this->initUliCMS();

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
    public function themesList(): void
    {
        $this->initUliCMS();

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
     * Uninstall one or more themes
     *
     * @param array $themes one or more themes
     */
    public function themesRemove(array $themes): void
    {
        $this->initUliCMS();

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
     *
     * @param string $component name of the component
     * @param string $directory path to migrations directory
     * @param string $stop path to migrations directory
     */
    public function dbmigratorUp(
        string $component,
        string $directory,
        ?string $stop = null
    ): void {
        $this->initUliCMS();

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
        $this->initUliCMS();

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
     * Reset dbtrack table
     *
     * @param string $component name of the component
     */
    public function dbmigratorReset(?string $component = null): void
    {
        $this->initUliCMS();

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
     * List all applied sql migrations
     *
     * @param string $component name of the component
     */
    public function dbmigratorList(?string $component = null): void
    {
        $this->initUliCMS();

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
        $this->initUliCMS();

        $modules = new ModuleManager();
        $modules->sync();
    }

    /**
     * Run PHPUnit Tests
     * @param string $testFile test file to run
     */
    public function testsRun(string $testFile = ''): void
    {
        $command = 'vendor/bin/phpunit';

        system("{$command} {$testFile}");
    }

    /**
     * Run PHPUnit Tests and update snapshots
     * @param string $testFile test file to run
     */
    public function testsUpdateSnapshots(string $testFile = ''): void
    {
        $command = 'vendor/bin/phpunit -d --update-snapshots';
        if (DIRECTORY_SEPARATOR === '\\') {
            $command = str_replace('/', '\\', $command);
        }

        system("{$command} {$testFile}");
    }

    /**
     * Create the application's database
     */
    public function dbCreate(): void
    {
        $this->initUliCMS();

        Database::setEchoQueries(true);

        Database::createSchema($_ENV['DB_DATABASE']);
        Database::select($_ENV['DB_DATABASE']);
    }

    /**
     * Drop and recreate the application's database
     */
    public function dbMigrate(): void
    {
        $this->initUliCMS();

        Database::setEchoQueries(true);

        $additionalSql = isset($_ENV['DBMIGRATOR_INITIAL_SQL_FILES']) ? explode(';', $_ENV['DBMIGRATOR_INITIAL_SQL_FILES']) : [];
        $additionalSql = array_map('trim', $additionalSql);

        Database::setupSchemaAndSelect(
            $_ENV['DB_DATABASE'],
            $additionalSql
        );
    }

    /**
     * Drop the application's database
     */
    public function dbDrop(): void
    {
        $this->initUliCMS();

        Database::setEchoQueries(true);
        if (Database::isConnected()) {
            Database::dropSchema($_ENV['DB_DATABASE']);
        }
    }

    /**
     * Create and migrate the application's database
     */
    public function dbReset(): void
    {
        $this->initUliCMS();

        Database::setEchoQueries(true);

        $this->dbDrop();

        $this->dbMigrate();
    }

    /**
     * Execute cronjobs
     */
    public function cron(): void
    {
        $this->initUliCMS();

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
    public function buildPrepare(): void {
        $this->initUliCMS();
        $this->buildCopyChangelog();
        $this->buildPhpCsFixer();
        $this->buildLicenses();
    }

    /**
     * Run php-cs-fiyer
     */
    public function buildPhpCsFixer(): void {
       system('vendor/bin/robo build:php-cs-fixer');
    }

    /**
     * Copy changelog to core_info module
     */
    public function buildCopyChangelog(): void {
        $this->initUliCMS();
        FileSystem::copy(ULICMS_ROOT . '/../doc/changelog.txt', ULICMS_CONTENT . '/modules/core_info/changelog.txt', true);
    }

    /**
     * Generate license files
     */
    public function buildLicenses(): void
    {
        system('vendor/bin/php-legal-licenses generate --hide-version');
        system('node_modules/.bin/license-report --only=prod --output=json > licenses.json');
    }

    /**
     * Optimize resources
     */
    public function buildOptimizeResources(): void {
        $this->buildCleanupVendor();
        $this->buildCleanupNodeModules();
        // $this->buildOptimizeImages();
        $this->buildOptimizeSvg();
        $this->buildMinifyCSS();
        $this->buildMinifyJSON();
        $this->buildMinifyHTML();
    }

    /**
     * Optimize all image files
     */
    public function buildOptimizeImages(): void {
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
    public function buildOptimizeSvg(): void {
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

            system($cmd);
        }
    }

    /**
     * Cleanup vendor directory
     */
    public function buildCleanupVendor(): void {
        $this->cleanUpDirectory('vendor');
    }

    /**
     * Cleanup node_modules directory
     */
    public function buildCleanupNodeModules(): void {
        $this->cleanUpDirectory('node_modules');
    }

    /**
     * Minify CSS files
     */
    public function buildMinifyCSS(): void {

        system('minifyall -e css');
    }

    /**
     * Minify JSON files
     */
    public function buildMinifyJSON(): void {

        system('minifyall -e json,cjson');
    }

    /**
     * Minify HTML files
     */
    public function buildMinifyHTML(): void {
        system('minifyall -e html');
    }

    /**
     * Converts an old BaseConfig to .env file format
     */
    public function dotenvFromConfig(): void {
        $this->initUliCMS();

        $cfg = new CMSConfig();
        $converter = new ConfigurationToDotEnvConverter($cfg);
        $attributes = $converter->convertToArray();

        foreach($attributes as $key => $value) {
            $this->writeln("{$key}={$value}");
        }

        $converter->writeEnvFile();
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
                FileSystem::delete($path);
            }
            catch(IOException $e){
                $this->writeln('Errror ' . $path);
            }
        }
    }

    protected function initUliCMS(): void
    {
        try {
            $this->initCore();
        } catch (Exception $e) {
            // If initialization failed, initialize at least ULICMS_ROOT
            // to pass direct access preventions
            if (! defined('ULICMS_ROOT')) {
                define('ULICMS_ROOT', dirname(__FILE__));
            }

            $this->showException($e);
        }
    }

    protected function showException(Exception $e): void
    {
        $this->writeln($e->getMessage());
    }

    protected function initCore(): void
    {
        if (! defined('ULICMS_ROOT')) {
            require dirname(__FILE__) . '/init.php';
            require_once getLanguageFilePath('en');
        }
    }

    private function showPageKeys($json): void
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
