#!/usr/bin/php -q
<?php
function sinstall_usage() {
	echo "sinstall - Install UliCMS package\n";
	echo "UliCMS Version " . cms_version () . "\n";
	echo "Copyright (C) 2015 by Ulrich Schmidt";
	echo "\n\n";
	echo "Usage php -f sinstall.php [file]\n\n";
	exit ();
}

if (php_sapi_name () != "cli") {
	die ( "This script can be run from command line only." );
}

$parent_path = dirname ( __file__ ) . "/../";
include $parent_path . "init.php";
include_once ULICMS_ROOT . "/classes/package_manager.php";
array_shift ( $argv );

// No time limit
@set_time_limit ( 0 );

$pkg = new PackageManager ();
if (count ( $argv ) == 0) {
	sinstall_usage ();
} else {
	$file = $argv [0];
	if (is_dir ( $file )) {
		echo "$file is a directory.";
	} else if (is_file ( $file )) {
		$pkg = new packageManager ();
		$result = $pkg->installPackage ( $file );
		if ($result) {
			echo "Package $file was successfully installed.";
		} else {
			echo "Error: Installation of package $file failed.";
		}
	} else {
		echo "No such file: $file";
	}
	echo "\n";
}