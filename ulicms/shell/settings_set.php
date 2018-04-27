#!/usr/bin/php -q
<?php

function usage()
{
    echo "settings_set - Set a setting\n";
    echo "UliCMS Version " . cms_version() . "\n";
    echo "Copyright (C) 2018 by Ulrich Schmidt";
    echo "\n\n";
    echo "Usage php -f settings_get.php [name] [value]\n\n";
    echo "[NULL] as value (including square brackets) for delete";
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

if (count($argv) != 2) {
    usage();
} else {
    $name = trim($argv[0]);
    $value = trim($argv[1]);
    if($value === "[NULL]"){
        Settings::delete($name);
    } else {
        Settings::set($name, $value);
    }
}