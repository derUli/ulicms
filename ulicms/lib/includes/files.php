<?php

declare(strict_types=1);

use App\Utils\File;

/**
 * Deletes a directory including its content
 * @param string $dir
 * @param bool $deleteMe
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

/**
 * Get all files in directory
 * @param string $dir
 * @return array
 */
function find_all_files(string $dir): array {
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
        foreach (find_all_files("$dir/$value") as $value) {
            $value = str_replace("\\", '/', $value);
            $result[] = $value;
        }
    }
    return $result;
}

function file_extension(string $filename): string {
    return File::getExtension($filename);
}

// Mimetypen einer Datei ermitteln
function get_mime(?string $file): string {
    return File::getMime($file);
}
