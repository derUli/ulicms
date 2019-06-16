<?php

function sureRemoveDir($dir, $deleteMe = true) {
    File::sureRemoveDir($dir, $deleteMe);
}

// Ordner rekursiv kopieren
function recurse_copy($src, $dst) {
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

function find_all_files($dir) {
    $root = scandir($dir);
    $result = array();
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

function find_all_folders($dir) {
    $root = scandir($dir);
    $result = array();
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

function file_extension($filename) {
    return File::getExtension($filename);
}

// Mimetypen einer Datei ermitteln
function get_mime($file) {
    return File::getMime($file);
}
