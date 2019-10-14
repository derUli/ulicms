#!/usr/bin/env php
<?php

function sinfo_usage() {
    echo "sinfo - List installed UliCMS packages\n";
    echo "UliCMS Version " . cms_version() . "\n";
    echo "Copyright (C) 2016 by Ulrich Schmidt";
    echo "\n\n";
    echo "Usage php -f sinfo.php [all|modules|themes|examine] [package to examine]\n\n";
    exit();
}

function sinfo_list_modules() {
    $modules = getAllModules();
    if (count($modules) > 0) {
        for ($i = 0; $i < count($modules); $i ++) {
            echo "- " . $modules[$i];
            $version = getModuleMeta($modules[$i], "version");
            if ($version !== null) {
                echo " " . $version;
            }
            echo "\n";
        }
    }

    echo "\n";
    echo count($modules) . " modules total\n";
}

function sinfo_list_themes() {
    $themes = getAllThemes();

    if (count($themes) > 0) {
        for ($i = 0; $i < count($themes); $i ++) {
            echo "- " . $themes[$i];
            $version = getThemeMeta($themes[$i], "version");
            if ($version !== null) {
                echo " " . $version;
            }
            echo "\n";
        }
    }
    echo "\n";
    echo count($themes) . " themes total\n";
}

if (php_sapi_name() != "cli") {
    die("This script can be run from command line only.");
}

$parent_path = dirname(__file__) . "/../";
require $parent_path . "init.php";
array_shift($argv);

// No time limit
@set_time_limit(0);

if (count($argv) > 2) {
    sinfo_usage();
} else {
    $type = "";
    if (isset($argv[0])) {
        $type = strtolower($argv[0]);
    }
    if ($type === "") {
        $type = "all";
    }
    switch ($type) {
        case "modules":
            sinfo_list_modules();
            break;
        case "themes":
            sinfo_list_themes();
            break;

        case "examine":
            if (count($argv) < 2) {
                sinfo_usage();
            }
            $file = $argv[1];
            if (!file_exists($file)) {
                echo "File " . basename($file) . " not found!\n";
                exit();
            }
            $json = json_decode(file_get_contents($file), true);
            ksort($json);
            $skipAttributes = array(
                "data",
                "screenshot"
            );
            foreach ($json as $key => $value) {
                if (in_array($key, $skipAttributes)) {
                    continue;
                }
                if (is_array($value)) {
                    $processedValue = implode(", ", $value);
                } else {
                    $processedValue = $value;
                }
                echo "$key: $processedValue\n";
            }
            echo $file;
            exit();
            break;
        case "all":
            echo "Modules:\n";
            sinfo_list_modules();
            echo "________________________________________";
            echo "\n\n";
            echo "Themes:\n";
            sinfo_list_themes();
            break;
        default:
            sinfo_usage();
            break;
    }
}