#!/usr/bin/env php
<?php
if (php_sapi_name () != "cli") {
	die ( "This script can be run from command line only." );
}

$parent_path = dirname ( __file__ ) . "/../";
include $parent_path . "init.php";
echo "UliCMS Release " . cms_version () . "\n";