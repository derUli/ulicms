<?php

const CORE_COMPONENT = 'update';

require_once dirname(__FILE__) . '/init.php';

use App\Database\DBMigrator;
use App\Backend\UliCMSVersion;

// Enable maintenance mode
Settings::set('maintenance_mode', '1');

use App\Utils\CacheUtil;

// no time limit to prevent a timeout while running sql migrations
@set_time_limit(0);

// Run SQL Migration Scripts
$migrator = new DBMigrator('core', 'lib/migrations/up');
$migrator->migrate();

$version = new UliCMSVersion();
$versionNumber = $version->getInternalVersionAsString();
Settings::set('db_schema_version', $versionNumber);

Settings::set('maintenance_mode', '0');

// Clear Cache
CacheUtil::clearCache();

// Disable maintenance mode
Settings::set('maintenance_mode', '0');

// The line below will be uncommented by the mk-upgrade-package.py deploy script
// The script will delete itself after execution.
// @unlink ("update.php");
// Redirect to Admin backend after running the update script
Response::redirect('admin/');
