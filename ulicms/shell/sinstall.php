#!/usr/bin/php -q
<?php
$language = "en";

function sinstall_usage() {
	echo "sinstall - Install an UliCMS package\n";
	echo "UliCMS Version " . cms_version () . "\n";
	echo "Copyright (C) 2015 - 2016 by Ulrich Schmidt";
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

include getLanguageFilePath ( $language );

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
		$result = false;
		if (endsWith ( $file, ".tar.gz" )) {
			$pkg = new PackageManager ();
			$result = $pkg->installPackage ( $file );
		} else if (endsWith ( $file, ".sin" )) {
			$pkg = new SinPackageInstaller ( $file );
			$is_installable = $pkg->isInstallable();
			if($is_installable){
			$result = $pkg->installPackage ();
			} else {
				foreach($pkg->getErrors() as $error){
					echo "$error\n";
				}
				exit();
			}
		} else {
			translate ( "not_supported_format" );
			echo "\n";
			exit();
		}
		
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