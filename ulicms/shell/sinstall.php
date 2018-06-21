#!/usr/bin/env php
<?php
$language = "en";

function sinstall_usage()
{
    echo "sinstall - Install an UliCMS package\n";
    echo "UliCMS Version " . cms_version() . "\n";
    echo "Copyright (C) 2015 - 2018 by Ulrich Schmidt";
    echo "\n\n";
    echo "Usage php -f sinstall.php [file]\n\n";
    exit();
}

if (php_sapi_name() != "cli") {
    die("This script can be run from command line only.");
}

$parent_path = dirname(__file__) . "/../";
include $parent_path . "init.php";
array_shift($argv);

include getLanguageFilePath($language);

// No time limit
@set_time_limit(0);

$pkg = new PackageManager();
if (count($argv) == 0) {
    sinstall_usage();
} else {
    $file = $argv[0];
    $cfg = new CMSConfig();
    if (is_url($file) and is_false($cfg->sinstall_allow_url)) {
        echo "Package installation from URLs is disabled.\n";
        echo "Add this to your CMSConfig.php to allow installations from URL.\n";
        echo 'var $sinstall_allow_url = true;';
    } else if (is_dir($file)) {
        echo "$file is a directory.";
    } else if (is_file($file) or is_url($file)) {
        $result = false;
        if (endsWith($file, ".tar.gz")) {
            $pkg = new PackageManager();
            $result = $pkg->installPackage($file);
        } else if (endsWith($file, ".sin")) {
            $pkg = new SinPackageInstaller($file);
            $is_installable = $pkg->isInstallable();
            if ($is_installable) {
                $result = $pkg->installPackage();
            } else {
                foreach ($pkg->getErrors() as $error) {
                    echo "$error\n";
                }
                exit();
            }
        } else {
            translate("not_supported_format");
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