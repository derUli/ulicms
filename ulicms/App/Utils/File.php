<?php

declare(strict_types=1);

namespace App\Utils;

use Intervention\MimeSniffer\MimeSniffer;

class File
{
    // write a string to a file
    public static function write(string $file, ?string $data): int
    {
        return file_put_contents($file, $data);
    }

    // append a string to a file
    public static function append(string $file, ?string $data): int
    {
        return file_put_contents($file, $data, FILE_APPEND | LOCK_EX);
    }

    // read a file and return it as string
    public static function read(string $file): ?string
    {
        return file_get_contents($file);
    }

    // delete a file
    public static function delete(string $file): bool
    {
        return unlink($file);
    }

    // rename a file
    public static function rename(string $old, string $new): bool
    {
        return rename($old, $new);
    }

    // output the last modification time of a file
    public static function lastChanged(string $file): void
    {
        echo self::getLastChanged($file);
    }

    // get the last modification time of a file
    public static function getLastChanged(string $file): int
    {
        clearstatcache();
        $retval = filemtime($file);
        clearstatcache();
        return $retval;
    }

    // return the extension of a file without dot
    // eg pdf, doc, jpg
    public static function getExtension(string $filename): string
    {
        $ext = explode(".", $filename);
        $ext = end($ext);
        $ext = strtolower($ext);
        return $ext;
    }

    // loads a (remote) file and split lines
    public static function loadLines(string $url): ?array
    {
        $data = file_get_contents_wrapper($url);
        if (!$data) {
            return null;
        }
        $data = normalizeLN($data, "\n");
        $data = explode("\n", $data);
        return $data;
    }

    // Delete a file  or a directory if it exist
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

    // load, split, and trim a remote file
    public static function loadLinesAndTrim(string $url): ?array
    {
        $data = self::loadLines($url);
        if ($data) {
            $data = array_map('trim', $data);
        }
        return $data;
    }

    // check if a file exists in the local file system
    public static function existsLocally(string $path): bool
    {
        return (preg_match('~^(\w+:)?//~', $path) === 0 && file_exists($path));
    }

    // converts a file to a data URI
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
}
