#!/usr/bin/env php
<?php

function usage() {
    echo "dg_migrator - Apply database migrations\n";
    echo "UliCMS Version " . cms_version() . "\n";
    echo "Copyright (C) 2018 by Ulrich Schmidt";
    echo "\n\n";
    echo "Usage php -f db_migrator.php [up|down|list|reset] [component] [sqldir] [stopmigration]\n";
    exit();
}

if (php_sapi_name() != "cli") {
    die("This script can be run from command line only.");
}
$parent_path = dirname(__file__) . "/../";
require $parent_path . "init.php";

array_shift($argv);

if (count($argv) == 0) {
    usage();
}

$command = $argv[0];
$component = count($argv) >= 2 ? $argv[1] : null;
$component = strtolower($component) !== "[NULL]" ? $component : null;

$directory = count($argv) >= 3 ? $argv[2] : null;
$stop = count($argv) >= 4 ? $argv[3] : null;

if ($command == "list") {
    $where = $component ? "component='" . Database::escapeValue($component) . "'" : "1=1";
    $result = Database::query("Select * from {prefix}dbtrack where $where order by component, date", true);
    while ($row = Database::fetchObject($result)) {
        echo "{$row->component} | {$row->name} | {$row->date}\n";
    }
    echo "\n";
    exit();
}
if ($command == "reset") {
    $migrator = new DBMigrator($component, getcwd());
    if ($component) {
        $migrator->resetDBTrack();
    } else {
        $migrator->resetDBTrackAll();
    }
    exit();
}
if ($command == "up") {
    if (!$component or ! $directory) {
        usage();
    }
    $folder = Path::resolve($directory . "/up");

    $migrator = new DBMigrator($component, $folder);
    try {
        Database::setEchoQueries(true);
        $migrator->migrate($stop);
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    } finally {
        Database::setEchoQueries(false);
    }
    exit();
}

if ($command == "down") {
    if (!$component or ! $directory) {
        usage();
    }
    $folder = Path::resolve($directory . "/down");
    $migrator = new DBMigrator($component, $folder);
    try {
        $migrator->rollback($stop);
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }
    exit();
}

usage();
