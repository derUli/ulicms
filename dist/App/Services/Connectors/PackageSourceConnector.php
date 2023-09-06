<?php

declare(strict_types=1);

namespace App\Services\Connectors;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use Settings;

use function cms_version;
use function file_get_contents_wrapper;

class PackageSourceConnector {
    private $packageSourceUrl = null;

    private $data = null;

    /**
     * Constructor
     *
     * @param string|null $packageSourceUrl
     */
    public function __construct(?string $packageSourceUrl = null) {
        if (! $packageSourceUrl) {
            $packageSourceUrl = Settings::get('pkg_src');
        }

        $packageSourceUrl = str_replace(
            '{version}',
            cms_version(),
            $packageSourceUrl
        );

        $packageSourceUrl .= 'index.json';
        $this->packageSourceUrl = $packageSourceUrl;
    }

    /**
     * Fetch package source index
     *
     * @param bool $forceUpdate
     *
     * @return bool
     */
    public function fetch(bool $forceUpdate = false): bool {
        $json = file_get_contents_wrapper(
            $this->packageSourceUrl,
            $forceUpdate
        );

        if (! $json) {
            return false;
        }

        $this->data = json_decode($json);

        return true;
    }

    /**
     * Get package source Url
     *
     * @return string|null
     */
    public function getPackageSourceUrl(): ?string {
        return $this->packageSourceUrl;
    }

    /**
     * Get available packages
     *
     * @return array|null
     */
    public function getAllAvailablePackages(): ?array {
        if (! $this->data) {
            $this->fetch();
        }
        return $this->data;
    }

    /**
     * Get available version of a package
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getVersionOfPackage(string $name): ?string {
        if (! $this->data) {
            $this->fetch();
        }
        foreach ($this->data as $package) {
            if ($package->name == $name) {
                return $package->version;
            }
        }
        return null;
    }

    public function getDataOfPackage(string $name): ?object {
        if (! $this->data) {
            $this->fetch();
        }
        foreach ($this->data as $package) {
            if ($package->name == $name) {
                return $package;
            }
        }
        return null;
    }

    /**
     * Get license of a package
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getLicenseOfPackage(string $name): ?string {
        if (! $this->data) {
            $this->fetch();
        }
        foreach ($this->data as $package) {
            if ($package->name == $name) {
                return $package->license;
            }
        }
        return null;
    }
}
