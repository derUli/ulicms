<?php

declare(strict_types=1);

namespace App\UliCMS;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Constants\DateTime;
use App\Exceptions\ConnectionFailedException;
use App\Exceptions\SqlException;
use App\Helpers\StringHelper;
use App\Models\Content\TypeMapper;
use App\Models\Content\Types\DefaultContentTypes;
use App\Registries\HelperRegistry;
use App\Registries\LoggerRegistry;
use App\Registries\ModelRegistry;
use App\Storages\Settings\DotEnvLoader;
use App\Storages\Vars;
use App\Utils\Logger;
use ControllerRegistry;
use Database;
use ModuleManager;
use mysqli;
use Nette\Utils\FileSystem;
use Path;
use Response;
use Settings;
use Throwable;
use User;

use function App\Utils\Session\sessionDestroy;
use function App\Utils\Session\sessionName;
use function App\Utils\Session\sessionStart;
use function do_event;
use function is_debug_mode;
use function send_header;

/**
 * This classes initialized UliCMS Core
 */
class CoreBootstrap {
    private string $rootDir;

    /**
     * @var (callable(Throwable): void)|null
     */
    private $exceptionHandler;

    /**
     * Constructor
     *
     * @param $rootDir Absolute path of ULICMS_ROOT
     */
    public function __construct(string $rootDir) {
        $this->rootDir = $rootDir;
    }

    /** Init UliCMS */
    public function init(): void {
        $this->setExceptionHandler();
        $this->definePathConstants();

        // If there is no new or old config redirect to installer
        if(! $this->checkConfigExists() && $this->getInstallerUrl()) {
            Response::redirect($this->getInstallerUrl());
        }

        $this->initStorages();
        $this->loadEnvFile();
        $this->createDirectories();
        $this->initLoggers();
        $this->connectDatabase();

        if ($this->isAutomigrateEnabled()) {
            $select = $this->autoMigrate();
        }

        $select = $this->selectDatabase();

        Database::setEchoQueries(false);

        if (! $select) {
            throw new SqlException('<h1>Database ' . $_ENV['DB_DATABASE'] . ' doesn\'t exist.</h1>');
        }

        // Preload all settings for performance reasons
        Settings::getAll();

        // Run this code only after first call after update
        if($this->isFreshDeploy()) {
            $this->postDeployUpdate();
        }

        $this->registerShutdownFunction();
        $this->initLocale();
        $this->handleSession();

        define('CACHE_PERIOD', (int)Settings::get('cache_period'));

        // If setting enforce_https is set redirect http:// to https:///
        if ($this->shouldRedirectToSSL()) {
            $this->enforceSSL();
        }

        ModelRegistry::loadModuleModels();
        TypeMapper::loadMapping();
        HelperRegistry::loadModuleHelpers();
        ControllerRegistry::loadModuleControllers();

        do_event('before_init');
        do_event('init');
        do_event('after_init');

        DefaultContentTypes::initTypes();

    }

    /**
     * Define other path constants
     *
     * @return void
     */
    public function definePathConstants(): void {
        defined('ULICMS_CONTENT') || define('ULICMS_CONTENT', "{$this->rootDir}/content");
        defined('ULICMS_TMP') || define('ULICMS_TMP', ULICMS_CONTENT . '/tmp');
        defined('ULICMS_LOG') || define('ULICMS_LOG', ULICMS_CONTENT . '/log');
        defined('ULICMS_GENERATED_PUBLIC') || define('ULICMS_GENERATED_PUBLIC', ULICMS_CONTENT . '/generated/public');
        defined('ULICMS_GENERATED_PRIVATE') || define('ULICMS_GENERATED_PRIVATE', ULICMS_CONTENT . '/generated/private');
        /**
         * @deprecated since UliCMS 2023.3
         */
        defined('ULICMS_CONFIGURATIONS') || define('ULICMS_CONFIGURATIONS', ULICMS_CONTENT . '/configurations');
        defined('ULICMS_CACHE_BASE') || define('ULICMS_CACHE_BASE', ULICMS_CONTENT . '/cache');
        defined('ULICMS_CACHE') || define('ULICMS_CACHE', ULICMS_CACHE_BASE . '/legacy');
    }

    /**
     * Set exception handler
     *
     * @return (callable(Throwable): void)|null
     */
    public function setExceptionHandler() {
        if(! is_cli()) {
            $this->exceptionHandler = set_exception_handler('exception_handler');
        }

        return $this->exceptionHandler;
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

        $displayErrors = (int)is_debug_mode();
        $errorReporting = is_debug_mode() ? E_ALL : 0;

        ini_set('display_errors', $displayErrors);
        error_reporting($errorReporting);

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
        Settings::register('cache_period', (string)DateTime::ONE_DAY_IN_SECONDS);

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
        $locale = [];

        $var = (is_admin_dir() && isset($_SESSION['system_language'])) ?
                'locale_' . $_SESSION['system_language'] :
                'locale_' . getFrontendLanguage();

        $localeSetting = Settings::get($var) ?: Settings::get('locale');

        if ($localeSetting) {
            $locale = StringHelper::splitAndTrim($localeSetting);
            array_unshift($locale, LC_ALL);
        }

        @call_user_func_array('setlocale', $locale);
        date_default_timezone_set(Settings::get('timezone'));
    }

    /**
     * Handle user session
     *
     * @return void
     */
    public function handleSession(): void {
        sessionName(Settings::get('session_name'));

        // Session abgelaufen
        if (isset($_SESSION['session_begin'])) {
            $session_timeout = 60 * Settings::get('session_timeout');

            if (time() - $_SESSION['session_begin'] > $session_timeout) {
                sessionDestroy();
                send_header('Location: ./');
                exit();
            }

            $_SESSION['session_begin'] = time();
        }

        do_event('before_session_start');

        // initialize session
        sessionStart();

        do_event('after_session_start');

        // If is logged in update last action
        if (is_logged_in()) {
            $user = new User(get_user_id());
            $user->setLastAction(time());
        }
    }

    /**
     * Should enforce https:// URLs
     *
     * @return bool
     */
    public function shouldRedirectToSSL(): bool {
        return Settings::get('enforce_https') !== null;
    }

    /**
     * Do redirect to https://
     *
     * @return void
     */
    public function enforceSSL(): void {
        if(! is_ssl()) {
            return;
        }

        send_header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        exit();
    }

    /**
     * Register shutdown function
     *
     * @return void
     */
    public function registerShutdownFunction(): void {
        register_shutdown_function(
            static function(): void {
                do_event('shutdown');

                $dbmigratorDropDatabaseOnShutdown = isset($_ENV['DBMIGRATOR_DROP_DATABASE_ON_SHUTDOWN']) && $_ENV['DBMIGRATOR_DROP_DATABASE_ON_SHUTDOWN'];

                if ($dbmigratorDropDatabaseOnShutdown) {
                    if (is_cli()) {
                        Database::setEchoQueries(true);
                    }

                    Database::dropSchema($_ENV['DB_DATABASE']);
                    Database::setEchoQueries(false);
                }
            }
        );
    }

    /**
     * Connect to database
     *
     * @throws ConnectionFailedException
     *
     * @return ?mysqli
     */
    public function connectDatabase(): ?mysqli {
        $dbServer = $_ENV['DB_SERVER'];
        $dbUser = $_ENV['DB_USER'];
        $dbPassword = $_ENV['DB_PASSWORD'];
        $dbPort = (int)($_ENV['DB_PORT'] ?? ini_get('mysqli.default_port'));
        $dbSocket = isset($_ENV['DB_SOCKET']) ? (string)$_ENV['DB_SOCKET'] : (string)ini_get('mysqli.default_socket');
        $dbStrictMode = isset($_ENV['DB_STRICT_MODE']) && $_ENV['DB_STRICT_MODE'];

        $connection = Database::connect(
            $dbServer,
            $dbUser,
            $dbPassword,
            $dbPort,
            $dbSocket,
            $dbStrictMode
        );

        if (! $connection) {
            throw new ConnectionFailedException('Can\'t connect to Database.');
        }

        return $connection;
    }

    /**
     * Check if automigrate is enabled
     *
     * @return bool
     */
    public function isAutomigrateEnabled(): bool {
        return isset($_ENV['DBMIGRATOR_AUTO_MIGRATE']) && $_ENV['DBMIGRATOR_AUTO_MIGRATE'];
    }

    /**
     * Do automigrate
     *
     * @return bool
     */
    public function autoMigrate(): bool {
        $additionalSql = isset($_ENV['DBMIGRATOR_INITIAL_SQL_FILES']) ? StringHelper::splitAndTrim($_ENV['DBMIGRATOR_INITIAL_SQL_FILES']) : [];
        $additionalSql = array_map('trim', $additionalSql);

        if (is_cli()) {
            Database::setEchoQueries(true);
        }

        return Database::setupSchemaAndSelect(
            $_ENV['DB_DATABASE'],
            $additionalSql
        );
    }

    /**
     * Select database
     *
     * @return bool
     */
    public function selectDatabase(): bool {
        return Database::select($_ENV['DB_DATABASE']);
    }
}
