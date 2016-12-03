#!/usr/bin/php -q
<?php
function sinfo_usage() {
	echo "sinfo - List installed UliCMS packages\n";
	echo "UliCMS Version " . cms_version () . "\n";
	echo "Copyright (C) 2016 by Ulrich Schmidt";
	echo "\n\n";
	echo "Usage php -f sinfo.php [all|modules|themes]\n\n";
	exit ();
}
function sinfo_list_modules() {
	$modules = getAllModules ();
	if (count ( $modules ) > 0) {
		for($i = 0; $i < count ( $modules ); $i ++) {
			echo "- " . $modules [$i];
			$version = getModuleMeta ( $modules [$i], "version" );
			if ($version !== null) {
				echo " " . $version;
			}
			echo "\n";
		}
	}
	
	echo "\n";
	echo count ( $modules ) . " modules total\n";
}
function sinfo_list_themes() {
	$themes = getThemeList ();
	
	if (count ( $themes ) > 0) {
		for($i = 0; $i < count ( $themes ); $i ++) {
			echo "- " . $themes [$i];
			$version = getThemeMeta ( $themes [$i], "version" );
			if ($version !== null) {
				echo " " . $version;
			}
			echo "\n";
		}
	}
	echo "\n";
	echo count ( $themes ) . " themes total\n";
}

if (php_sapi_name () != "cli") {
	die ( "This script can be run from command line only." );
}

$parent_path = dirname ( __file__ ) . "/../";
include $parent_path . "init.php";
include_once ULICMS_ROOT . "/classes/objects/pkg/package_manager.php";
array_shift ( $argv );

// No time limit
@set_time_limit ( 0 );

if (count ( $argv ) > 1) {
	sinfo_usage ();
} else {
	$type = "";
	if (isset ( $argv [0] )) {
		$type = strtolower ( $argv [0] );
	}
	if ($type === "") {
		$type = "all";
	}
	switch ($type) {
		case "modules" :
			sinfo_list_modules ();
			break;
		case "themes" :
			sinfo_list_themes ();
			break;
		case "all" :
			echo "Modules:\n";
			sinfo_list_modules ();
			echo "________________________________________";
			echo "\n\n";
			echo "Themes:\n";
			sinfo_list_themes ();
			break;
		default :
			sinfo_usage ();
			break;
	}
}