<?php

declare(strict_types=1);

namespace App\Utils;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

use Intervention\MimeSniffer\MimeSniffer;
use Nette\Utils\FileSystem;

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
        if(! file_exists($file)) {
            return false;
        }

        FileSystem::delete($file);
        return true;
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
        return (string)$type;
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
        FileSystem::delete($dir);

        if(! $deleteMe) {
            FileSystem::createDir($dir);
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
            if (is_dir("{$dir}/{$value}")) {
                $result[] = str_replace('\\', '/', "{$dir}/{$value}");
                $result = array_merge($result, self::findAllDirs("{$dir}/{$value}"));
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

            if (is_file("{$dir}/{$value}")) {
                $result[] = str_replace('\\', '/', "{$dir}/{$value}");
                continue;
            }

            foreach (self::findAllFiles("{$dir}/{$value}") as $value) {
                $value = str_replace('\\', '/', $value);
                $result[] = $value;
            }
        }
        return $result;
    }
}
