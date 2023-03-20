<?php

declare(strict_types=1);

namespace App\Packages;

defined('ULICMS_ROOT') or exit('no direct script access allowed');

use Database;
use ZipArchive;
use App\Utils\CacheUtil;
use UliCMSVersion;

use function file_get_contents_wrapper;
use function sureRemoveDir;
use function recurse_copy;

/**
 * This class is used for handling patches
 */
class PatchManager
{
    /**
     * Get names of all installed patches
     * @return array
     */
    public function getInstalledPatchNames(): array
    {
        $retval = [];
        $result = Database::query(
            "SELECT name from " . tbname("installed_patches")
        );
        while ($row = Database::fetchObject($result)) {
            $retval[] = $row->name;
        }
        return $retval;
    }

    /**
     * Fetch list of all available patches
     * @param bool $noCache
     * @return string|null
     */
    public function fetchPackageIndex(bool $noCache = true): ?string
    {
        return file_get_contents_wrapper($this->getPatchCheckUrl(), $noCache);
    }

    /**
     * Get patch check URL
     * @return string
     */
    protected function getPatchCheckUrl(): string
    {
        $installed_patches = $this->getInstalledPatchNames();
        $installed_patches = implode(";", $installed_patches);
        $version = new UliCMSVersion();

        return "https://patches.ulicms.de/?v=" . urlencode(implode('.', $version->getInternalVersion())) . "&installed_patches=" . urlencode($installed_patches);
    }

    /**
     * Get available patches for this installation
     * @return array
     */
    public function getAvailablePatches(): array
    {
        $patches = [];
        $indexData = $this->fetchPackageIndex();
        if (!$indexData) {
            return $patches;
        }

        $lines = \App\Helpers\StringHelper::linesFromString($indexData, true, true);
        foreach ($lines as $line) {
            $splittedLine = explode("|", $line);
            $patches[] = new Patch(
                $splittedLine[0],
                $splittedLine[1],
                $splittedLine[2],
                $splittedLine[3]
            );
        }
        return $patches;
    }

    /**
     * Truncate list of installed patches
     * @return bool
     */
    public function truncateInstalledPatches(): bool
    {
        return Database::truncateTable("installed_patches");
    }

    /**
     * Get list of installed patches
     * @return array
     */
    public function getInstalledPatches(): array
    {
        $retval = [];
        $result = Database::query(
            "SELECT * from " . tbname("installed_patches")
        );
        while ($row = Database::fetchObject($result)) {
            $retval[$row->name] = $row;
        }
        return $retval;
    }

    /**
     * Installs a patch
     * @param string $name
     * @param string $description
     * @param string $url
     * @param bool $clear_cache
     * @param string|null $checksum
     * @return bool
     */
    public function installPatch(
        string $name,
        string $description,
        string $url,
        bool $clear_cache = true,
        ?string $checksum = null
    ): bool {
        @set_time_limit(0);
        $test = $this->getInstalledPatchNames();
        if (in_array($name, $test)) {
            return false;
        }

        $tmp_dir = ULICMS_TMP . '/' . uniqid() . '/';
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
        if ($zip->open($download_tmp) === true) {
            $zip->extractTo($tmp_dir);
            $patch_dir = $tmp_dir . "patch";
            $zip->close();
            if (is_dir($patch_dir)) {
                recurse_copy($patch_dir, ULICMS_ROOT);
                $name = Database::escapeValue($name);
                $description = Database::escapeValue($description);
                $url = Database::escapeValue($url);
                Database::query("INSERT INTO " . tbname("installed_patches") .
                        " (name, description, url, date) VALUES "
                        . "('$name', '$description', '$url', NOW())");

                sureRemoveDir($tmp_dir, true);
                if ($clear_cache) {
                    CacheUtil::clearCache();
                }
                return true;
            }
        }
        sureRemoveDir($tmp_dir, true);
        if ($clear_cache) {
            CacheUtil::clearCache();
        }
        return false;
    }
}
