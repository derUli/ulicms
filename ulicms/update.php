<?php

const CORE_COMPONENT = 'update';

require_once "init.php";

use App\Packages\PatchManager;
use App\Utils\CacheUtil;

// no time limit to prevent a timeout while running sql migrations
@set_time_limit(0);

// Run SQL Migration Scripts
$migrator = new DBMigrator("core", "lib/migrations/up");
$migrator->migrate();

$version = new UliCMSVersion();
$versionNumber = $version->getInternalVersionAsString();
Settings::set("db_schema_version", $versionNumber);

// Reset tracking of installed patches
$patchManager = new PatchManager();
$patchManager->truncateInstalledPatches();

CacheUtil::clearCache();

// The line below will be uncommented by the mk-upgrade-package.py deploy script
// The script will delete itself after execution.
// @unlink ("update.php");
// Redirect to Admin backend after running the update script
Response::redirect("admin/");
