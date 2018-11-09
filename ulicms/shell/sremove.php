#!/usr/bin/env php
<?php

function sremove_usage()
{
    echo "sremove - Remove an UliCMS package\n";
    echo "UliCMS Version " . cms_version() . "\n";
    echo "Copyright (C) 2016 by Ulrich Schmidt";
    echo "\n\n";
    echo "Usage php -f sremove.php [module|theme] [package]\n\n";
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

if (count($argv) < 2) {
    sremove_usage();
}

$packages = $argv;
array_shift($packages);

$type = strtolower($argv[0]);

$allowedTypes = array(
    "module",
    "theme"
);

if (! faster_in_array($type, $allowedTypes)) {
    sremove_usage();
}

foreach ($packages as $package) {
    if (uninstall_module($package, $type)) {
        echo "Package $package removed\n";
    } else {
        
        echo "Removing  $package failed.\n";
    }
}