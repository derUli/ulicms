<?php

declare(strict_types=1);

namespace App\Packages;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Backend\UliCMSVersion;
use App\Utils\File;
use App\Utils\VersionComparison;
use Database;
use Path;

/**
 * Util to install Simple Install packages.
 */
class SinPackageInstaller {
    private $file = null;

    private $errors = [];

    private $packageData = null;

    /**
     * Constructor
     * @param string $file
     */
    public function __construct(string $file) {
        if (! empty($file)) {
            $this->file = $file;
        }
    }

    /**
     * Load package data
     * @return array
     */
    public function loadPackage(): array {
        // If already loaded
        if ($this->packageData) {
            return $this->packageData;
        }

        // Load file
        $data = file_get_contents($this->file);

        // Decode json to assoc
        $json = json_decode($data, true);
        $this->packageData = $json;

        return $json;
    }

    /**
     * Extract archive data
     * @return string
     */
    public function extractArchive(): string {
        $path = Path::resolve('ULICMS_TMP/package-' . $this->getProperty('id')
                        . '-' . $this->getProperty('version') . '.tar.gz');
        $data = $this->loadPackage();

        // Decode base64 data payload
        $decoded = base64_decode($data['data']);

        file_put_contents($path, $decoded);
        return $path;
    }

    /**
     * Install package
     * @param bool $clear_cache
     * @return bool
     */
    public function installPackage(bool $clear_cache = true): bool {
        if ($this->isInstallable()) {
            $path = $this->extractArchive();
            $pkg = new PackageManager();
            $result = $pkg->installPackage($path, $clear_cache);

            File::deleteIfExists($path);

            return $result;
        }

        return false;
    }

    /**
     * Get size of package data payload
     * @return int
     */
    public function getSize(): int {
        $data = $this->loadPackage();
        $decoded = base64_decode($data['data']);
        return mb_strlen($decoded, '8bit');
    }

    /**
     * Get json property if is set
     * @param string $name
     * @return type
     */
    public function getProperty(string $name) {
        $data = $this->loadPackage();
        if (isset($data[$name]) && ! empty(
            $data[$name]
        )
        ) {
            return $data[$name];
        }
        return null;
    }

    /**
     * Get installation errors
     * @return array
     */
    public function getErrors(): array {
        return $this->errors;
    }

    /**
     * Check if package is installable
     * @return bool
     */
    public function isInstallable(): bool {
        $this->errors = [];
        $installed_modules = getAllModules();
        $data = $this->loadPackage();

        if (isset($data['dependencies']) && is_array($data['dependencies'])) {
            $dependencies = $data['dependencies'];

            foreach ($dependencies as $dependency) {
                if (! in_array($dependency, $installed_modules)) {
                    $this->errors[] = get_translation(
                        'dependency_x_is_not_installed',
                        [
                            '%x%' => $dependency
                        ]
                    );
                }
            }
        }

        $version = new UliCMSVersion();
        $version = $version->getInternalVersionAsString();

        $version_not_supported = false;

        if (isset($data['compatible_from']) && ! empty($data['compatible_from']) && ! VersionComparison::compare($version, $data['compatible_from'], '>=')) {
            $version_not_supported = true;
        }

        if (isset($data['compatible_to']) && ! empty($data['compatible_to']) && ! VersionComparison::compare($version, $data['compatible_to'], '<=')) {
            $version_not_supported = true;
        }

        $phpVersionSupported = true;

        // if package requires a specific php version check it
        if (isset($data['min_php_version']) && ! empty($data['min_php_version']) && ! VersionComparison::compare(PHP_VERSION, $data['min_php_version'], '>=')) {
            $phpVersionSupported = false;
        }

        if (isset($data['max_php_version']) && ! empty($data['max_php_version']) && ! VersionComparison::compare(PHP_VERSION, $data['max_php_version'], '<=')) {
            $phpVersionSupported = false;
        }

        if (! $phpVersionSupported) {
            $this->errors[] = get_translation('php_version_x_not_supported', [
                '%version%' => PHP_VERSION
            ]);
        }

        $mysqlVersion = preg_replace('/[^0-9.].*/', '', Database::getServerVersion());

        $mysqlVersionSupported = true;

        // if package requires a specific mysql version check it
        if (
            isset($data['min_mysql_version']) &&
            ! empty($data['min_mysql_version']) &&
            ! VersionComparison::compare($mysqlVersion, $data['min_mysql_version'], '>=')
        ) {
            $mysqlVersionSupported = false;
        }

        if (
            isset($data['max_mysql_version']) &&
            ! empty($data['max_mysql_version']) &&
            ! VersionComparison::compare(
                $mysqlVersion,
                $data['max_mysql_version'],
                '<='
            )
        ) {
            $mysqlVersionSupported = false;
        }

        if (! $mysqlVersionSupported) {
            $this->errors[] = get_translation(
                'mysql_version_x_not_supported',
                [
                    '%version%' => $mysqlVersion
                ]
            );
        }

        if (
            isset($data['required_php_extensions']) &&
            is_array($data['required_php_extensions'])
        ) {
            $loadedExtensions = get_loaded_extensions();

            foreach ($data['required_php_extensions'] as $extension) {
                if (! in_array($extension, $loadedExtensions)) {
                    $this->errors[] = get_translation(
                        'php_extension_x_not_installed',
                        [
                            '%extension%' => $extension
                        ]
                    );
                }
            }
        }

        if ($version_not_supported) {
            $this->errors[] = get_translation(
                'this_ulicms_version_is_not_supported'
            );
        }

        $decoded = base64_decode($data['data']);
        $sha_hash = sha1($decoded);
        if ($sha_hash != $data['checksum']) {
            $this->errors[] = get_translation('sha1_checksum_not_equal');
        }

        return count($this->errors) <= 0;
    }
}
