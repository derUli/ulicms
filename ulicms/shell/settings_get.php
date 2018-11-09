#!/usr/bin/env php
<?php

function usage()
{
    echo "settings_get - Get a setting\n";
    echo "UliCMS Version " . cms_version() . "\n";
    echo "Copyright (C) 2018 by Ulrich Schmidt";
    echo "\n\n";
    echo "Usage php -f settings_get.php [name]\n\n";
    exit();
}

if (php_sapi_name() != "cli") {
    die("This script can be run from command line only.");
}

$parent_path = dirname(__file__) . "/../";
include $parent_path . "init.php";
array_shift($argv);

// No time limit
@set_time_limit(0);

if (count($argv) != 1) {
    usage();
} else {
    $setting = Settings::get($argv[0]);
    if ($setting !== false) {
        echo $setting;
    } else {
        echo "[NULL]";
    }
    echo "\n";
}