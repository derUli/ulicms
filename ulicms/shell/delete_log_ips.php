#!/usr/bin/php -q
<?php
if (php_sapi_name () != "cli") {
	die ( "This script can be run from command line only." );
}

$parent_path = dirname ( __file__ ) . "/../";
include $parent_path . "init.php";
array_shift ( $argv );

db_query ( "update ".tbname("log"). " SET ip = null") or die ( db_error () . "\n" );
echo "IP addresses was removed from log table.\n";
exit ();
