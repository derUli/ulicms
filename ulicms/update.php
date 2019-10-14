<?php

$configFile = "CMSConfig.php";

// since UliCMS 2018.3 the config file has a new name
if (file_exists("cms-config.php") and ! file_exists($configFile)) {
    // update config file
    $content = file_get_contents("cms-config.php");
    $content = str_replace("class config", "class CMSConfig", $content);
    file_put_contents("cms-config.php", $content);
    // rename config file
    rename("cms-config.php", $configFile);
}

require_once "init.php";

// "var" is old and should not be used in PHP >= 5
// if the config file is writable replace "var" with "public"
if (is_writable($configFile)) {
    $configContent = file_get_contents($configFile);
    if (str_contains('var $', $configContent)) {
        $configContent = str_ireplace('var $', 'public $', $configContent);
        file_put_contents($configFile, $configContent);
    }
}

if (!is_dir(ULICMS_CONFIGURATIONS)) {
    mkdir(ULICMS_CONFIGURATIONS);
}

$defaultConfig = Path::resolve("ULICMS_CONFIGURATIONS/default.php");

if (!file_exists($defaultConfig)) {
    rename($configFile, $defaultConfig);
}

copy(Path::resolve("ULICMS_ROOT/lib/CMSConfigSample.php"), Path::resolve("ULICMS_ROOT/CMSConfig.php"));

// no time limit to prevent a timeout while running sql migrations
@set_time_limit(0);

// Run SQL Migration Scripts
$migrator = new DBMigrator("core", "lib/migrations/up");
$migrator->migrate();

// Enable HTML Minifying
Settings::register("minify_html", "1");

// Patch Manager zurücksetzen
$pkg = new PackageManager();
$pkg->truncateInstalledPatches();

// The line below will be uncommented by the mk-upgrade-package.py deploy script
// The script will delete itself after execution.
// @unlink ("update.php");
// Redirect to Admin backend after running the update script
Response::redirect("admin/");
