<?php

declare(strict_types=1);

use UliCMS\Services\Connectors\PackageSourceConnector;
use UliCMS\Constants\PackageTypes;

class PackageManager
{
    public function checkForNewerVersionOfPackage(string $name): ?string
    {
        $connector = new PackageSourceConnector();
        $connector->fetch(true);
        return $connector->getVersionOfPackage($name);
    }

    public function splitPackageName(string $name): array
    {
        $name = str_ireplace(".tar.gz", "", $name);
        $name = str_ireplace(".zip", "", $name);
        $splitted = explode("-", $name);
        $version = array_pop($splitted);
        $name = $splitted;
        return [
            join("-", $name),
            $version
        ];
    }

    public function isInstalled(
        string $package,
        string $type = PackageTypes::TYPE_MODULE
    ): bool {
        switch ($type) {
            case PackageTypes::TYPE_MODULE:
                $module = new Module($package);
                return $module->isInstalled();
            case PackageTypes::TYPE_THEME:
                return faster_in_array($package, getAllThemes());
            default:
                throw new BadMethodCallException(
                    "Package Type {$type} not supported"
                );
        }
    }

    // TODO: Reimplement in PackageSourceconnector
    public function installPackage(
        string $file,
        bool $clear_cache = true
    ): bool {
        @set_time_limit(0);
        try {
            // Paket entpacken
            $phar = new PharData($file);
            $phar->extractTo(ULICMS_DATA_STORAGE_ROOT, null, true);

            // make asset files of the package public
            if (startsWith(ULICMS_DATA_STORAGE_ROOT, "gs://")
                    and class_exists("GoogleCloudHelper")) {
                GoogleCloudHelper::makeFilesPublic(ULICMS_DATA_STORAGE_ROOT);
            }

            $post_install_script1 = ULICMS_DATA_STORAGE_ROOT .
                    DIRECTORY_SEPARATOR . "post-install.php";
            $post_install_script2 = ULICMS_TMP .
                    DIRECTORY_SEPARATOR . "post-install.php";

            // post_install_script ausführen und anschließend
            // entfernen, sofern vorhanden;
            if (file_exists($post_install_script1)) {
                require_once $post_install_script1;
                unlink($post_install_script1);
            } elseif (file_exists($post_install_script2)) {
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

    public function getInstalledModules(): array
    {
        $availableModules = [];

        $moduleFolder = Path::resolve("ULICMS_DATA_STORAGE_ROOT/content/modules");
        $moduleDirectories = find_all_folders($moduleFolder);

        natcasesort($moduleDirectories);
        foreach ($moduleDirectories as $moduleDirectory) {
            $metadataFile = "{$moduleDirectory}/metadata.json";

            if (file_exists($metadataFile)) {
                $availableModules [] = basename($moduleDirectory);
            }
        }
        natcasesort($availableModules);
        return $availableModules;
    }

    public function getInstalledThemes(): array
    {
        $themes = [];
        $templateDir = Path::resolve(
            "ULICMS_DATA_STORAGE_ROOT/content/templates"
        ) . "/";

        $folders = scanDir($templateDir);
        natcasesort($folders);
        for ($i = 0; $i < count($folders); $i++) {
            $f = $templateDir . $folders[$i] . "/";
            if (is_dir($templateDir . $folders[$i]) && !startsWith($folders[$i], ".")) {
                array_push($themes, $folders[$i]);
            }
        }

        natcasesort($themes);

        return $themes;
    }

    public function getInstalledPackages(string $type = 'modules'): ?array
    {
        if ($type === 'modules' or $type === 'module') {
            return $this->getInstalledModules();
        } elseif ($type === 'themes' or $type === 'theme') {
            return $this->getInstalledThemes();
        }
        throw new BadMethodCallException("No such package type: $type");
    }
}
