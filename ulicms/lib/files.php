<?php

declare(strict_types=1);

use UliCMS\Utils\File;

/**
 * Deletes content of a direcotry
 * @param string $dir Directory
 * @param bool $deleteMe Delete the directory or just the content
 * @return void
 */
function sureRemoveDir(string $dir, bool $deleteMe = true): void {
    File::sureRemoveDir($dir, $deleteMe);
}

// Ordner rekursiv kopieren
function recurse_copy(string $src, string $dst): void {
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

function find_all_files(string $dir): array {
    $root = scandir($dir);
    $result = [];
    foreach ($root as $value) {
        if ($value === '.' || $value === '..') {
            continue;
        }
        if (is_file("$dir/$value")) {
            $result[] = str_Replace("\\", "/", "$dir/$value");
            continue;
        }
        foreach (find_all_files("$dir/$value") as $value) {
            $value = str_replace("\\", "/", $value);
            $result[] = $value;
        }
    }
    return $result;
}

function find_all_folders(string $dir): array {
    $root = scandir($dir);
    $result = [];
    foreach ($root as $value) {
        if ($value === '.' || $value === '..') {
            continue;
        }
        if (is_dir("$dir/$value")) {
            $result[] = str_Replace("\\", "/", "$dir/$value");
            $result = array_merge($result, find_all_folders("$dir/$value"));
            continue;
        }
    }
    return $result;
}

/**
 * Returns the extension of a file name
 * @param string $filename Filename
 * @return string Extension without dot (e.g. "jpg")
 */
function file_extension(string $filename): string {
    return File::getExtension($filename);
}

/**
 * Detect the mime type of a file by it's content
 * @param string|null $file File Name
 * @return string|null Mime ype or null
 */
function get_mime(?string $file): ?string {
    return File::getMime($file);
}
