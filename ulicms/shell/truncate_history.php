#!/usr/bin/env php
<?php
if (php_sapi_name() != "cli") {
    die("This script can be run from command line only.");
}

$parent_path = dirname(__file__) . "/../";
require $parent_path . "init.php";
array_shift($argv);

db_query("TRUNCATE TABLE " . tbname("history")) or die(db_error() . "\n");
echo "History was cleared\n";
exit();
