<?php

declare(strict_types=1);

namespace App\Packages;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Constants\PackageTypes;
use App\Services\Connectors\PackageSourceConnector;
use App\Utils\CacheUtil;
use App\Utils\File;
use BadMethodCallException;
use Module;
use Path;
use PharData;
use UnexpectedValueException ;

/**
 * This class is for handling packages
 *  */
class PackageManager
{
    /**
     * Check package source for a newer version of a package
     *
     * @param string $name
     *
     * @return string|null
     */
    public function checkForNewerVersionOfPackage(string $name): ?string
    {
        $connector = new PackageSourceConnector();
        $connector->fetch(true);
        return $connector->getVersionOfPackage($name);
    }

    /**
     * Check if a package is installed
     *
     * @param string $package
     * @param string $type
     * @throws BadMethodCallException
     *
     * @return bool
     */
    public function isInstalled(
        string $package,
        string $type = PackageTypes::TYPE_MODULE
    ): bool {
        switch ($type) {
            case PackageTypes::TYPE_MODULE:
                $module = new Module($package);
                return $module->isInstalled();
            case PackageTypes::TYPE_THEME:
                return in_array($package, getAllThemes());
            default:
                throw new BadMethodCallException(
                    "Package Type {$type} not supported"
                );
        }
    }

    /**
     * Install a package from file
     *
     * @param string $file
     * @param bool $clear_cache
     *
     * @return bool
     */
    public function installPackage(
        string $file,
        bool $clear_cache = true
    ): bool {
        @set_time_limit(0);
        try {
            // Paket entpacken
            $phar = new PharData($file);
            $phar->extractTo(ULICMS_ROOT, null, true);


            $postInstallScripts = [
                Path::Resolve('ULICMS_ROOT/post-install.php'),
                Path::Resolve('ULICMS_TMP/post-install.php')
            ];

            foreach ($postInstallScripts as $script) {
                if (is_file($script)) {
                    include $script;
                    unlink($script);
                }
            }

            if ($clear_cache) {
                CacheUtil::clearCache();
            }

            $success = true;
        } catch (UnexpectedValueException $e) {
            if ($clear_cache) {
                CacheUtil::clearCache();
            }

            $success = false;
        }

        return $success;
    }

    /**
     * Get installed modules
     *
     * @return array<string>
     */
    public function getInstalledModules(): array
    {
        $availableModules = [];

        $moduleFolder = Path::resolve('ULICMS_ROOT/content/modules');
        $moduleDirectories = File::findAllDirs($moduleFolder);

        natcasesort($moduleDirectories);
        foreach ($moduleDirectories as $moduleDirectory) {
            $metadataFile = "{$moduleDirectory}/metadata.json";

            if (is_file($metadataFile)) {
                $availableModules [] = basename($moduleDirectory);
            }
        }
        natcasesort($availableModules);
        return $availableModules;
    }

    /**
     * Get installed themes
     *
     * @return array<string>
     */
    public function getInstalledThemes(): array
    {
        $themes = [];
        $templateDir = Path::resolve(
            'ULICMS_ROOT/content/templates'
        ) . '/';

        $folders = scandir($templateDir);
        natcasesort($folders);

        $foldersCount = count($folders);
        for ($i = 0; $i < $foldersCount; $i++) {
            $f = $templateDir . $folders[$i] . '/';
            if (is_dir($templateDir . $folders[$i]) && ! str_starts_with($folders[$i], '.')) {
                $themes[] = $folders[$i];
            }
        }

        natcasesort($themes);

        return $themes;
    }

    /**
     * Get installed packages
     *
     * @param string $type
     * @throws BadMethodCallException
     *
     * @return array<string>|null
     */
    public function getInstalledPackages(string $type = 'modules'): ?array
    {
        if ($type === 'modules' || $type === 'module') {
            return $this->getInstalledModules();
        } elseif ($type === 'themes' || $type === 'theme') {
            return $this->getInstalledThemes();
        }
        throw new BadMethodCallException("No such package type: {$type}");
    }
}
