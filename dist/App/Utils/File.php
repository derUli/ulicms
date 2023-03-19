<?php

declare(strict_types=1);

namespace App\Utils;

defined('ULICMS_ROOT') or exit('no direct script access allowed');

use Intervention\MimeSniffer\MimeSniffer;

/**
 * Utils for handling files
 */
class File
{
    /**
     * Get the last modification time of a file as Unix timestamp
     * @param string $file
     * @return int
     */
    public static function getLastChanged(string $file): int
    {
        clearstatcache();
        $retval = filemtime($file);
        clearstatcache();
        return $retval;
    }

    /**
     * Get the extension of a file without dot in lower case
     * @param string $filename
     * @return string
     */
    public static function getExtension(string $filename): string
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        return strtolower($ext);
    }

    // Delete a file or a directory if it exist
    public static function deleteIfExists(string $file): bool
    {
        if (is_file($file)) {
            return unlink($file);
        } elseif (is_dir($file)) {
            sureRemoveDir($file, true);
            return !is_file($file);
        }
        return false;
    }

    /**
     * Convert a file to a data: Uri
     * @param string $file
     * @param string|null $mime
     * @return string|null
     */
    public static function toDataUri(string $file, ?string $mime = null): ?string
    {
        $url = null;

        if (is_file($file)) {
            $mime = $mime ?? File::getMime($file);
            $data = file_get_contents($file);
            $base64_data = base64_encode($data);
            $url = "data:{$mime};base64,{$base64_data}";
        }
        return $url;
    }

    /**
     * Detect the mime type of a file based on its content
     * @param string $file
     * @return string
     */
    public static function getMime(string $file): string
    {
        $sniffer = new MimeSniffer();
        $sniffer->setFromFilename($file);

        $type = $sniffer->getType();
        return (string) $type;
    }

    /**
     * Deletes a directory including its content
     * @param string $dir
     * @param bool $deleteMe
     * @return void
     */
    public static function sureRemoveDir(
        string $dir,
        bool $deleteMe = true
    ): void {
        if (!is_dir($dir)) {
            return;
        }

        $dh = opendir($dir);

        while (false !== ($obj = readdir($dh))) {
            if ($obj == '.' || $obj == '..') {
                continue;
            }
            $path = "$dir/$obj";

            if (is_dir($path)) {
                sureRemoveDir($path, true);
            } elseif (is_file($path)) {
                unlink($path);
            }
        }

        closedir($dh);
        if ($deleteMe) {
            @rmdir($dir);
        }
    }

    /**
     * From a list of file get the timestamp of the last changed file
     * @param array $files
     * @return int|null
     */
    public static function getNewestMtime(array $files): ?int
    {
        $mtime = 0;
        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) > $mtime) {
                $mtime = filemtime($file);
            }
        }
        return $mtime;
    }

    /**
     * Get all folders in directory
     * @param string $dir
     * @return array
     */
    public static function findAllDirs(string $dir): array
    {
        $root = scandir($dir);
        $result = [];
        foreach ($root as $value) {
            if ($value === '.' || $value === '..') {
                continue;
            }
            if (is_dir("$dir/$value")) {
                $result[] = str_Replace("\\", '/', "$dir/$value");
                $result = array_merge($result, self::findAllDirs("$dir/$value"));
                continue;
            }
        }
        return $result;
    }

    /**
     * Get all files in directory
     * @param string $dir
     * @return array
     */
    public static function findAllFiles(string $dir): array
    {
        $root = scandir($dir);
        $result = [];

        foreach ($root as $value) {
            if ($value === '.' || $value === '..') {
                continue;
            }

            if (is_file("$dir/$value")) {
                $result[] = str_Replace("\\", '/', "$dir/$value");
                continue;
            }

            foreach (self::findAllFiles("$dir/$value") as $value) {
                $value = str_replace("\\", '/', $value);
                $result[] = $value;
            }
        }
        return $result;
    }
}
