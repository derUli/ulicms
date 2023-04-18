<?php

const CORE_COMPONENT = 'update';

$rootDir = dirname(__FILE__);

require_once $rootDir . '/init.php';

use App\Backend\UliCMSVersion;
use App\Database\DBMigrator;
use App\Utils\CacheUtil;

// Enable maintenance mode
Settings::set('maintenance_mode', '1');

// no time limit to prevent a timeout while running sql migrations
@set_time_limit(0);

// Run SQL Migration Scripts
$migrator = new DBMigrator('core', 'lib/migrations/up');
$migrator->migrate();

$version = new UliCMSVersion();
$versionNumber = $version->getInternalVersionAsString();
Settings::set('db_schema_version', $versionNumber);

// Clear Cache
CacheUtil::clearCache();

// Disable maintenance mode
Settings::set('maintenance_mode', '0');

// The line below will be uncommented by the mk-upgrade-package.py deploy script
// The script will delete itself after execution.
// @unlink ("update.php");

// If this script is called by CLI exit here
if (is_cli()) {
    exit;
}

// Redirect to Admin backend after running the update script
Response::redirect('admin/');
