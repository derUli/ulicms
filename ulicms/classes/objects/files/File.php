<?php

declare(strict_types=1);

namespace UliCMS\Utils;

class File {

    // write a string to a file
    public static function write(string $file, ?string $data): int {
        return file_put_contents($file, $data);
    }

    // append a string to a file
    public static function append(string $file, ?string $data): int {
        return file_put_contents($file, $data, FILE_APPEND | LOCK_EX);
    }

    // read a file and return it as string
    public static function read(string $file): ?string {
        return file_get_contents($file);
    }

    // delete a file
    public static function delete(string $file): bool {
        return unlink($file);
    }

    // rename a file
    public static function rename(string $old, string $new): bool {
        return rename($old, $new);
    }

    // output the last modification time of a file
    public static function lastChanged(string $file): void {
        echo self::getLastChanged($file);
    }

    // get the last modification time of a file
    public static function getLastChanged(string $file): int {
        clearstatcache();
        $retval = filemtime($file);
        clearstatcache();
        return $retval;
    }

    // return the extension of a file without dot
    // eg pdf, doc, jpg
    public static function getExtension(string $filename): string {
        $ext = explode(".", $filename);
        $ext = end($ext);
        $ext = strtolower($ext);
        return $ext;
    }

    // loads a (remote) file and split lines
    public static function loadLines(string $url): ?array {
        $data = file_get_contents_wrapper($url);
        if (!$data) {
            return null;
        }
        $data = normalizeLN($data, "\n");
        $data = explode("\n", $data);
        return $data;
    }

    // Delete a file  or a directory if it exist
    public static function deleteIfExists(string $file): bool {
        if (file_exists($file) and is_file($file)) {
            return unlink($file);
        } else if (file_exists($file) and is_dir($file)) {
            sureRemoveDir($file, true);
            return !file_exists($file);
        }
        return false;
    }

    // load, split, and trim a remote file
    public static function loadLinesAndTrim(string $url): ?array {
        $data = self::loadLines($url);
        if ($data) {
            $data = array_map('trim', $data);
        }
        return $data;
    }

    // check if a file exists in the local file system
    public static function existsLocally(string $path): bool {
        return ( preg_match('~^(\w+:)?//~', $path) === 0
                and file_exists($path));
    }

    // converts a file to a data URI
    public static function toDataUri(string $file, ?string $mime = null): ?string {
        $url = null;
        if (file_exists($file)) {
            $mime = is_null($mime) ? get_mime($file) : $mime;
            $data = file_get_contents($file);
            $base64_data = base64_encode($data);
            $url = "data:{$mime};base64,{$base64_data}";
        }
        return $url;
    }

    // detect the mime type of a file
    public static function getMime(string $file): ?string {
        // try multiple methods to detect mime type,
        // based on the php environment
        if (function_exists("finfo_file")) {
            // return mime type ala mimetype extension
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file);
            finfo_close($finfo);
            return $mime;
        } else if (function_exists("mime_content_type")) {
            return mime_content_type($file);
        } else if (!stristr(ini_get("disable_functions"), "shell_exec")) {
            // http://stackoverflow.com/a/134930/1593459
            $file = escapeshellarg($file);
            $mime = shell_exec("file -bi " . $file);
            return $mime;
        }
        // if detection of file mimetype failed
        return null;
    }

    public static function sureRemoveDir(
            string $dir, bool $deleteMe = true
    ): void {
        if (!is_dir($dir)) {
            return;
        }

        if (!$dh = @opendir($dir)) {
            return;
        }

        while (false !== ($obj = readdir($dh))) {
            if ($obj == '.' || $obj == '..') {
                continue;
            }
            $path = "$dir/$obj";

            if (is_dir($path)) {
                sureRemoveDir($path, true);
            } else if (is_file($path)) {
                unlink($path);
            }
        }

        closedir($dh);
        if ($deleteMe) {
            @rmdir($dir);
        }
    }

    public static function getNewestMtime(array $files): ?int {
        $mtime = 0;
        foreach ($files as $file) {
            if (file_exists($file) and filemtime($file) > $mtime) {
                $mtime = filemtime($file);
            }
        }
        return $mtime;
    }

}
