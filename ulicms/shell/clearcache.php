#!/usr/bin/env php
<?php
if (php_sapi_name() != "cli") {
    die("This script can be run from command line only.");
}

$parent_path = dirname(__file__) . "/../";
require $parent_path . "init.php";
array_shift($argv);

clearCache();
echo "Cache was cleared\n";
exit();