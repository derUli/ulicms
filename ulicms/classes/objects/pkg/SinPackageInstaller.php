<?php

class SinPackageInstaller {

    private $file = null;
    private $errors = array();

    public function __construct($file) {
        if (StringHelper::isNotNullOrEmpty($file)) {
            $this->file = $file;
        }
    }

    private function loadPackage() {
        $data = file_get_contents($this->file);
        $json = json_decode($data, true);
        return $json;
    }

    public function extractArchive() {
        $path = Path::resolve("ULICMS_TMP/package-" . $this->getProperty("id") . "-" . $this->getProperty("version") . ".tar.gz");
        $data = $this->loadPackage();
        $decoded = base64_decode($data["data"]);
        file_put_contents($path, $decoded);
        return $path;
    }

    public function installPackage($clear_cache = true) {
        if ($this->isInstallable()) {
            $path = $this->extractArchive();
            $pkg = new PackageManager();
            $result = $pkg->installPackage($path, $clear_cache);
            unlink($path);
            return $result;
        } else {
            return false;
        }
    }

    public function getSize() {
        $data = $this->loadPackage();
        $decoded = base64_decode($data["data"]);
        $size = mb_strlen($decoded, '8bit');
        return $size;
    }

    public function getProperty($name) {
        $data = $this->loadPackage();
        if (isset($data[$name]) and StringHelper::isNotNullOrEmpty($data[$name])) {
            return $data[$name];
        } else {
            return null;
        }
    }

    public function getErrors() {
        return $this->errors;
    }

    public function isInstallable() {
        $this->errors = array();
        $installed_modules = getAllModules();
        $data = $this->loadPackage();
        if (isset($data["dependencies"]) and is_array($data["dependencies"])) {
            $dependencies = $data["dependencies"];
            foreach ($dependencies as $dependency) {
                if (!faster_in_array($dependency, $installed_modules)) {
                    $this->errors[] = get_translation("dependency_x_is_not_installed", array(
                        "%x%" => $dependency
                    ));
                }
            }
        }
        $version = new UliCMSVersion();
        $version = $version->getInternalVersionAsString();
        $version_not_supported = false;
        if (isset($data["compatible_from"]) and StringHelper::isNotNullOrEmpty($data["compatible_from"])) {
            if (!version_compare($version, $data["compatible_from"], ">=")) {
                $version_not_supported = true;
            }
        }

        if (isset($data["compatible_to"]) and StringHelper::isNotNullOrEmpty($data["compatible_to"])) {
            if (!version_compare($version, $data["compatible_to"], "<=")) {
                $version_not_supported = true;
            }
        }

        $phpVersionSupported = true;

        // if package requires a specific php version check it
        if (isset($data["min_php_version"]) and StringHelper::isNotNullOrEmpty($data["min_php_version"])) {
            if (!version_compare(phpversion(), $data["min_php_version"], ">=")) {
                $phpVersionSupported = false;
            }
        }

        if (isset($data["max_php_version"]) and StringHelper::isNotNullOrEmpty($data["max_php_version"])) {
            if (!version_compare(phpversion(), $data["max_php_version"], "<=")) {
                $phpVersionSupported = false;
            }
        }
        if (!$phpVersionSupported) {
            $this->errors[] = get_translation("php_version_x_not_supported", array(
                "%version%" => phpversion()
            ));
        }

        $mysqlVersion = Database::getServerVersion();
        $mysqlVersion = preg_replace('/[^0-9.].*/', '', $mysqlVersion);

        $mysqlVersionSupported = true;

        // if package requires a specific mysql version check it
        if (isset($data["min_mysql_version"]) and StringHelper::isNotNullOrEmpty($data["min_mysql_version"])) {
            if (!version_compare($mysqlVersion, $data["min_mysql_version"], ">=")) {
                $mysqlVersionSupported = false;
            }
        }

        if (isset($data["max_mysql_version"]) and StringHelper::isNotNullOrEmpty($data["max_mysql_version"])) {
            if (!version_compare($mysqlVersion, $data["max_mysql_version"], "<=")) {
                $mysqlVersionSupported = false;
            }
        }

        if (!$mysqlVersionSupported) {
            $this->errors[] = get_translation("mysql_version_x_not_supported", array(
                "%version%" => $mysqlVersion
            ));
        }

        if (isset($data["required_php_extensions"]) and is_array($data["required_php_extensions"])) {
            $loadedExtensions = get_loaded_extensions();
            foreach ($data["required_php_extensions"] as $extension) {
                if (!in_array($extension, $loadedExtensions)) {
                    $this->errors[] = get_translation("php_extension_x_not_installed", array(
                        "%extension%" => $extension
                    ));
                }
            }
        }

        if ($version_not_supported) {
            $this->errors[] = get_translation("this_ulicms_version_is_not_supported");
        }

        $decoded = base64_decode($data["data"]);
        $sha_hash = sha1($decoded);
        if ($sha_hash != $data["checksum"]) {
            $this->errors[] = get_translation("sha1_checksum_not_equal");
        }

        return (count($this->errors) <= 0);
    }

}
