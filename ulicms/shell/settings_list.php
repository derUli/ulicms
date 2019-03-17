#!/usr/bin/env php
<?php
if (php_sapi_name() != "cli") {
    die("This script can be run from command line only.");
}

$parent_path = dirname(__file__) . "/../";
require $parent_path . "init.php";

// show all settings
$settings = Settings::getAll();
foreach ($settings as $setting) {
    if (empty($setting->name)) {
        continue;
    }
    echo "{$setting->name}: {$setting->value}\n";
}
