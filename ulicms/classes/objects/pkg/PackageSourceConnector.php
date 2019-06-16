<?php

namespace UliCMS\Services\Connectors;

use Settings;
use function cms_version;
use function file_get_contents_wrapper;

class PackageSourceConnector {

    private $packageSourceUrl = null;
    private $data = null;

    public function __construct($packageSourceUrl = null) {
        if (!$packageSourceUrl) {
            $packageSourceUrl = Settings::get("pkg_src");
        }
        $packageSourceUrl = str_replace("{version}", cms_version(),
                $packageSourceUrl);
        $packageSourceUrl .= "index.json";
        $this->packageSourceUrl = $packageSourceUrl;
    }

    public function fetch($forceUpdate = false) {
        $json = file_get_contents_wrapper($this->packageSourceUrl, $forceUpdate);
        if (!$json) {
            return false;
        }
        $this->data = json_decode($json);
        return true;
    }

    public function getPackageSourceUrl() {
        return $this->packageSourceUrl;
    }

    public function getAllAvailablePackages() {
        if (!$this->data) {
            $this->fetch();
        }
        return $this->data;
    }

    public function getVersionOfPackage($name) {
        if (!$this->data) {
            $this->fetch();
        }
        foreach ($this->data as $package) {
            if ($package->name == $name) {
                return $package->version;
            }
        }
        return null;
    }

    public function getDataOfPackage($name) {
        if (!$this->data) {
            $this->fetch();
        }
        foreach ($this->data as $package) {
            if ($package->name == $name) {
                return $package;
            }
        }
        return null;
    }

    public function getLicenseOfPackage($name) {
        if (!$this->data) {
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
