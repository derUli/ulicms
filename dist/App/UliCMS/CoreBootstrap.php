<?php

declare(strict_types=1);

namespace App\UliCMS;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Storages\Settings\DotEnvLoader;
use App\Storages\Vars;

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
     * Check if an .env file or an old format config file exists
     *
     * @return bool
     */
    public function checkConfigExists(): bool {
        $oldConfigFile = $this->rootDir . '/CMSConfig.php';
        $newConfigFile = DotEnvLoader::envFilenameFromEnvironment(get_environment());

        return is_file($oldConfigFile) || is_file($newConfigFile);
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
}
