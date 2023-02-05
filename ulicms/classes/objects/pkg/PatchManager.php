<?php

declare(strict_types=1);

namespace UliCMS\Packages;

use Database;
use ZipArchive;
use StringHelper;
use UliCMS\Utils\CacheUtil;
use function file_get_contents_wrapper;
use function sureRemoveDir;
use function recurse_copy;

class PatchManager {

    public function getInstalledPatchNames(): array {
        $retval = [];
        $result = Database::query(
                        "SELECT name from " . tbname("installed_patches")
        );
        while ($row = Database::fetchObject($result)) {
            $retval[] = $row->name;
        }
        return $retval;
    }

    public function fetchPackageIndex(): ?string {
        return file_get_contents_wrapper(PATCH_CHECK_URL, true);
    }

    public function getAvailablePatches(): array {
        $patches = [];
        $indexData = $this->fetchPackageIndex();
        if (!$indexData) {
            return $patches;
        }

        $lines = StringHelper::linesFromString($indexData, true, true);
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

    public function truncateInstalledPatches(): bool {
        return Database::truncateTable("installed_patches");
    }

    public function getInstalledPatches(): array {
        $retval = [];
        $result = Database::query(
                        "SELECT * from " . tbname("installed_patches")
        );
        while ($row = Database::fetchObject($result)) {
            $retval[$row->name] = $row;
        }
        return $retval;
    }

    public function installPatch(
            string $name,
            string $description,
            string $url,
            bool $clear_cache = true,
            ?string $checksum = null
    ): bool {
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
