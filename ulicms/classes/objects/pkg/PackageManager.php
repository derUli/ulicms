<?php

use UliCMS\Services\Connectors\PackageSourceConnector;
use UliCMS\Constants\PackageTypes;

class PackageManager {

    public function checkForNewerVersionOfPackage($name) {
        $connector = new PackageSourceConnector();
        $connector->fetch(true);
        return $connector->getVersionOfPackage($name);
    }

    public function splitPackageName($name) {
        $name = str_ireplace(".tar.gz", "", $name);
        $name = str_ireplace(".zip", "", $name);
        $splitted = explode("-", $name);
        $version = array_pop($splitted);
        $name = $splitted;
        return array(
            join("-", $name),
            $version
        );
    }

    public function getInstalledPatchNames() {
        $result = db_query("SELECT name from " . tbname("installed_patches"));
        $retval = [];
        while ($row = db_fetch_object($result)) {
            $retval[] = $row->name;
        }
        return $retval;
    }

    public function truncateInstalledPatches() {
        return db_query("TRUNCATE TABLE " . tbname("installed_patches"));
    }

    public function isInstalled($package, $type = PackageTypes::TYPE_MODULE) {
        switch ($type) {
            case PackageTypes::TYPE_MODULE:
                $module = new Module($package);
                return $module->isInstalled();
            case PackageTypes::TYPE_THEME:
                return faster_in_array($package, getAllThemes());
            default:
                throw new NotImplementedException("Package Type {$type} not supported");
        }
    }

    public function installPatch($name, $description, $url, $clear_cache = true, $checksum = null) {
        @set_time_limit(0);
        $test = $this->getInstalledPatchNames();
        if (faster_in_array($name, $test)) {
            return false;
        }

        $tmp_dir = ULICMS_TMP . "/" . uniqid() . "/";
        if (!is_dir($tmp_dir)) {
            mkdir($tmp_dir);
        }
        $download = file_get_contents_wrapper($url, true, $checksum);

        $download_tmp = $tmp_dir . "patch.zip";

        if (!$download) {
            return false;
        }

        file_put_contents($download_tmp, $download);
        $zip = new ZipArchive();
        if ($zip->open($download_tmp) === TRUE) {
            $zip->extractTo($tmp_dir);
            $patch_dir = $tmp_dir . "patch";
            $zip->close();
            if (is_dir($patch_dir)) {
                recurse_copy($patch_dir, ULICMS_ROOT);
                $name = db_escape($name);
                $description = db_escape($description);
                $url = db_escape($url);
                db_query("INSERT INTO " . tbname("installed_patches") . " (name, description, url, date) VALUES ('$name', '$description', '$url', NOW())");

                sureRemoveDir($tmp_dir, true);
                if ($clear_cache) {
                    clearCache();
                }
                return true;
            }
        }
        sureRemoveDir($tmp_dir, true);
        if ($clear_cache) {
            clearCache();
        }
        return false;
    }

    public function getInstalledPatches() {
        $result = db_query("SELECT * from " . tbname("installed_patches"));
        $retval = [];
        while ($row = db_fetch_object($result)) {
            $retval[$row->name] = $row;
        }
        return $retval;
    }

    // TODO: Reimplement in PackageSourceconnector
    public function installPackage($file, $clear_cache = true) {
        @set_time_limit(0);
        try {
            // Paket entpacken
            $phar = new PharData($file);
            $phar->extractTo(ULICMS_DATA_STORAGE_ROOT, null, true);

            // make asset files of the package public
            if (startsWith(ULICMS_DATA_STORAGE_ROOT, "gs://") and class_exists("GoogleCloudHelper")) {
                GoogleCloudHelper::makeFilesPublic(ULICMS_DATA_STORAGE_ROOT);
            }

            $post_install_script1 = ULICMS_DATA_STORAGE_ROOT . DIRECTORY_SEPARATOR . "post-install.php";
            $post_install_script2 = ULICMS_TMP . DIRECTORY_SEPARATOR . "post-install.php";

            // post_install_script ausführen und anschließend
            // entfernen, sofern vorhanden;
            if (file_exists($post_install_script1)) {
                require_once $post_install_script1;
                unlink($post_install_script1);
            } else if (file_exists($post_install_script2)) {
                require_once $post_install_script2;
                unlink($post_install_script2);
            }

            if ($clear_cache) {
                clearCache();
            }
            return true;
        } catch (Exception $e) {
            if ($clear_cache) {
                clearCache();
            }
            return false;
        }
    }

    public function getInstalledModules() {
        $module_folder = Path::resolve("ULICMS_DATA_STORAGE_ROOT/content/modules") . "/";

        $available_modules = [];
        $directory_content = scandir($module_folder);

        natcasesort($directory_content);
        for ($i = 0; $i < count($directory_content); $i ++) {
            if (is_dir($module_folder . $directory_content[$i])) {
                $module_init_file = $module_folder . $directory_content[$i] . "/" . $directory_content[$i] . "_main.php";
                $module_init_file2 = $module_folder . $directory_content[$i] . "/" . "main.php";
                $metadata_file = $module_folder . $directory_content[$i] . "/metadata.json";
                if (file_exists($metadata_file)) {
                    array_push($available_modules, $directory_content[$i]);
                } else if ($directory_content[$i] != ".." and $directory_content[$i] != ".") {
                    if (file_exists($module_init_file) or file_exists($module_init_file2)) {
                        array_push($available_modules, $directory_content[$i]);
                    }
                }
            }
        }
        natcasesort($available_modules);
        return $available_modules;
    }

    public function getInstalledThemes() {
        $themes = [];
        $templateDir = Path::resolve("ULICMS_DATA_STORAGE_ROOT/content/templates") . "/";

        $folders = scanDir($templateDir);
        natcasesort($folders);
        for ($i = 0; $i < count($folders); $i ++) {
            $f = $templateDir . $folders[$i] . "/";
            if (is_dir($templateDir . $folders[$i]) and ! startsWith($folders[$i], ".")) {
                array_push($themes, $folders[$i]);
            }
        }

        natcasesort($themes);

        return $themes;
    }

    public function getInstalledPackages($type = 'modules') {
        if ($type === 'modules') {
            return $this->getInstalledModules();
        } else if ($type === 'themes') {
            return $this->getInstalledThemes();
        }
        return null;
    }

}
