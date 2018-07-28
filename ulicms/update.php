<?php
$configFile = "CMSConfig.php";

// since UliCMS 2018.3 the config file has a new name
if (is_file("cms-config.php") and ! is_file($configFile)) {
    // update config file
    $content = file_get_contents("cms-config.php");
    $content = str_replace("class config", "class CMSConfig", $content);
    file_put_contents("cms-config.php", $content);
    // rename config file
    rename("cms-config.php", $configFile);
}

include_once "init.php";

// "var" is old and should not be used in PHP >= 5
// if the config file is writable replace "var" with "public"
if (is_writable($configFile)) {
    $configContent = file_get_contents($configFile);
    if (str_contains('var $', $configContent)) {
        $configContent = str_ireplace('var $', 'public $', $configContent);
        file_put_contents($configFile, $configContent);
    }
}

// no time limit to prevent a timeout while running sql migrations
@set_time_limit(0);

Database::query("ALTER TABLE `{prefix}users` DROP COLUMN `avatar_file`", true);
Database::query("DELETE FROM `{prefix}settings` where name in ('comment_mode', 'facebook_id', 'disqus_id');");
Settings::delete("disable_html_validation");
Database::query("CREATE TABLE `{prefix}dbtrack` ( `id` INT NOT NULL AUTO_INCREMENT , `component` VARCHAR(150) NOT NULL , `name` VARCHAR(150) NOT NULL , `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;", true);

Settings::set("db_schema_version", "2018.4");

// Run SQL Migration Scripts
$migrator = new DBMigrator("core", "lib/updates/up");
$migrator->disableStrictMode();
$migrator->migrate();

// register new settings
Settings::register("min_time_to_fill_form", "0");

Settings::register("smtp_encryption", "");

// PEAR Mail Feature is removed change to default email mode
if (Settings::get("email_mode") === "pear_mail") {
    Settings::set("email_mode", "internal");
}

// Patch Manager zurücksetzen
$pkg = new PackageManager();
$pkg->truncateInstalledPatches();

// The line below will be uncommented by the mk-upgrade-package.py deploy script
// The script will delete itself after execution.
// @unlink ("update.php");

// Redirect to Admin backend after running the update script
Response::redirect("admin/");
