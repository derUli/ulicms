<?php

const CORE_COMPONENT = 'update';

use App\Backend\UliCMSVersion;
use App\Database\DBMigrator;
use App\Storages\Settings\ConfigurationToDotEnvConverter;
use App\Storages\Settings\DotEnvLoader;
use App\Utils\CacheUtil;

$rootDir = dirname(__FILE__);
$oldConfigFile = $rootDir . '/CMSConfig.php';

try {
    // Init will fail after upgrade to 2023.3 since the configuration files are not converted yet
    require_once $rootDir . '/init.php';
} catch(Exception $e) {
    $appEnv = get_environment();
    $newConfigFile = DotEnvLoader::envFilenameFromEnvironment($appEnv);

    // If there is no .env file but a CMSConfig.php in ULICMS_ROOT migrate config
    if(is_file($oldConfigFile) && ! is_file($newConfigFile)) {
        require $oldConfigFile;

        $oldConfig = new CMSConfig();

        $converter = new ConfigurationToDotEnvConverter($oldConfig);

        // If conversion successful
        if($converter->writeEnvFile()) {

            // Prepend required variable APP_ENV to .env file
            $fileContent = file_get_contents($newConfigFile);
            $fileContent = "APP_ENV={$appEnv}" . PHP_EOL . $fileContent;
            file_put_contents($newConfigFile, $fileContent);

            @unlink($oldConfigFile);

            // CLI: Tell the user to run update.php again
            if (is_cli()) {
                exit('Configuration file converted. Please run update.php again.' . PHP_EOL);
            }

            // Web: Execute update.php again
            Response::redirect('update.php');
        } else {
            // If conversion failed
            exit('Converting configuration file failed.');
        }
    }
    // All other exceptions
    exit($e->getMessage());
}

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

// The line below will be uncommented by the mk-upgrade-package.py deploy script
// The script will delete itself after execution.
// @unlink ("update.php");

// If this script is called by CLI exit here
if (is_cli()) {
    echo 'OK';
    exit;
}

// Redirect to Admin backend after running the update script
Response::redirect('admin/');
