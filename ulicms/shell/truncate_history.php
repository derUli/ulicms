#!/usr/bin/php -q
<?php
if (php_sapi_name () != "cli") {
	die ( "This script can be run from command line only." );
}

$parent_path = dirname ( __file__ ) . "/../";
include $parent_path . "init.php";
array_shift ( $argv );

db_query ( "TRUNCATE TABLE " . tbname ( "history" ) ) or die ( db_error () . "\n" );
echo "History was cleared\n";
exit ();
