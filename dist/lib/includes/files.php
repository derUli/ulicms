<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

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

/**
 * Recursively copy directory
 * @param string $src
 * @param string $dst
 * @return void
 */
function recurse_copy(string $src, string $dst): void {
    $dir = opendir($src) ?: null;

    if(! is_dir($dst)) {
        mkdir($dst, 0777, true);
    }

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
