<?php

declare(strict_types=1);

namespace App\UliCMS;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Backend\UliCMSVersion;
use App\Constants\DateTimeConstants;
use App\Helpers\StringHelper;
use App\Registries\LoggerRegistry;
use App\Storages\Settings\DotEnvLoader;
use App\Storages\Vars;
use App\Utils\Logger;
use ModuleManager;
use Nette\Utils\FileSystem;
use Path;
use Settings;

/**
 * This classes initialized UliCMS Core
 */
class CoreBootstrap {
    private string $rootDir;

    /**
     * Constructor
     *
     * @param $rootDir Absolute path of ULICMS_ROOT
     */
    public function __construct(string $rootDir) {
        $this->rootDir = $rootDir;
    }

    public function init(): void {

        // TOODO:
    }

    /**
     * Init storages
     */
    public function initStorages(): void {
        Vars::set('http_headers', []);
    }

    /**
     * Check if an .env file exists and an old config file doesn't exist
     *
     * @return bool
     */
    public function checkConfigExists(): bool {

        $newConfigFile = $this->rootDir . '/' . DotEnvLoader::envFilenameFromEnvironment(get_environment());

        return is_file($newConfigFile);
    }

    /**
     * If installer exists get relative installer url
     *
     * @return ?string
     */
    public function getInstallerUrl(): ?string {
        $installerDir = null;

        $dirs = [
            "{$this->rootDir}/installer.aus/index.php",
            "{$this->rootDir}/installer/index.php"
        ];

        foreach($dirs as $dir) {
            if(is_file($dir)) {
                $installerDir = './' . basename(dirname($dir));
            }
        }

        return $installerDir;
    }

    /**
     * Load env file
     */
    public function loadEnvFile(): void {
        $loader = DotEnvLoader::fromEnvironment($this->rootDir, get_environment());
        $loader->load();

        if ($_ENV['DEBUG']) {
            ini_set('display_errors', 1);
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 0);
            error_reporting(0);
        }

        // Set default umask for PHP created files
        if(isset($_ENV['UMASK'])) {
            umask((int)$_ENV['UMASK']);
        }
    }

    /**
     * Create required directories
     * @return void
     */
    public function createDirectories(): void {

        $createDirectories = [
            ULICMS_TMP,
            ULICMS_CACHE_BASE,
            ULICMS_CACHE,
            ULICMS_LOG,
            ULICMS_GENERATED_PUBLIC,
            ULICMS_GENERATED_PRIVATE,
        ];

        foreach($createDirectories as $dir) {
            if(! is_dir($dir)) {
                FileSystem::createDir($dir);
            }
        }

        // .htaccess file for all directories which must be not public accesible
        $htaccessForLogFolderSource = $this->rootDir . '/lib/htaccess-deny-all.txt';

        // Put .htaccess deny from all to this directories
        $secureDirectories =
        [
            ULICMS_TMP,
            ULICMS_LOG,
            ULICMS_GENERATED_PRIVATE
        ];

        foreach($secureDirectories as $dir) {
            $htaccessFile = "{$dir}/.htaccess";

            if (! is_file($htaccessFile)) {
                FileSystem::copy($htaccessForLogFolderSource, $htaccessFile);
            }
        }
    }

    /**
     * Initialize enabled loggers
     *
     * @return void
     */
    public function initLoggers(): void {
        if (isset($_ENV['EXCEPTION_LOGGING']) && $_ENV['EXCEPTION_LOGGING']) {
            LoggerRegistry::register(
                'exception_log',
                new Logger(Path::resolve('ULICMS_LOG/exception_log'))
            );
        }

        if (isset($_ENV['QUERY_LOGGING']) && $_ENV['QUERY_LOGGING']) {
            LoggerRegistry::register(
                'sql_log',
                new Logger(Path::resolve('ULICMS_LOG/sql_log'))
            );
        }

        if (isset($_ENV['PHPMAILER_LOGGING']) && $_ENV['PHPMAILER_LOGGING']) {
            LoggerRegistry::register(
                'phpmailer_log',
                new Logger(Path::resolve('ULICMS_LOG/phpmailer_log'))
            );
        }
    }

    /**
     * Check if this is a fresh deploy
     *
     * @return bool
     */
    public function isFreshDeploy(): bool {
        $initialized = Settings::get('initialized');

        $version = new UliCMSVersion();
        $buildTimestamp = (string)$version->getBuildTimestamp();

        return $initialized !== $buildTimestamp;
    }

    /**
     * This method is executed after a fresh installation or upgrade
     *
     * @return void
     */
    public function postDeployUpdate() {
        $moduleManager = new ModuleManager();
        $moduleManager->sync();

        Settings::register('session_name', uniqid() . '_SESSION');
        Settings::register('cache_period', (string)DateTimeConstants::ONE_DAY_IN_SECONDS);

        $version = new UliCMSVersion();
        $buildTimestamp = (string)$version->getBuildTimestamp();

        Settings::set('initialized', $buildTimestamp);
    }

    /**
     * Initialize locale
     *
     * @return void
     */
    public function initLocale(): void {
        $locale = Settings::get('locale');

        if ($locale) {
            $locale = StringHelper::splitAndTrim($locale);
            array_unshift($locale, LC_ALL);
            @call_user_func_array('setlocale', $locale);
        }

    }
}
