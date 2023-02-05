<?php

declare(strict_types=1);

namespace UliCMS\Utils;

use Path;
use Settings;
use Phpfastcache\Helper\Psr16Adapter;
use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;
use ModuleManager;
use function do_event;
use function sureRemoveDir;
use function get_request_uri;
use function getCurrentLanguage;
use function strbool;
use function is_mobile;
use function is_crawler;
use function is_tablet;
use ControllerRegistry;
use DesignSettingsController;

class CacheUtil {

    private static $adapter;

    // returns a Psr16 cache adapter if caching is enabled
    // or $force is true
    // else returns null
    public static function getAdapter(bool $force = false): ?Psr16Adapter {
        if (!self::isCacheEnabled() && !$force) {
            return null;
        }

        if (!is_null(self::$adapter)) {
            return self::$adapter;
        }

        $cacheConfig = array(
            "path" => Path::resolve("ULICMS_CACHE"),
            "defaultTtl" => self::getCachePeriod()
        );

        // Auto Detect which caching driver to use

        $driver = self::getDriverName();

        self::$adapter = new Psr16Adapter(
                $driver,
                new ConfigurationOption($cacheConfig)
        );

        return self::$adapter;
    }

    public static function getDriverName(): string {
        $driver = "files";

        return apply_filter($driver, 'cache_driver_name');
    }

    public static function resetAdapater() {
        CacheManager::clearInstances();
        self::$adapter = null;
        self::getAdapter(true);
    }

    // returns true if caching is enabled
    public static function isCacheEnabled(): bool {
        return !Settings::get("cache_disabled") && !is_logged_in();
    }

    // clears the page cache
    public static function clearPageCache(): void {
        $adapter = self::getAdapter();
        if ($adapter) {
            $adapter->clear();
        }
    }

    // clears all caches including opcache, cache directory
    // and tmp directory, sync modules directory with database
    public static function clearCache(): void {
        do_event("before_clear_cache");

        // clear opcache if available
        if (function_exists("opcache_reset")) {
            opcache_reset();
        }

        sureRemoveDir(Path::resolve("ULICMS_CACHE"), false);
        sureRemoveDir(Path::resolve("ULICMS_TMP"), false);

        // Sync modules table in database with modules folder
        $moduleManager = new ModuleManager();
        $moduleManager->sync();

        if (class_exists("DesignSettingsController")) {
            $designSettingsController = ControllerRegistry::get(
                            DesignSettingsController::class
            );
            $designSettingsController->_generateSCSSToFile();
        }

        do_event("after_clear_cache");
    }

    // Returns cache expiration time as integer
    public static function getCachePeriod(): int {
        return intval(Settings::get("cache_period"));
    }

    // generates an unique identifier for the current page
    public static function getCurrentUid(): string {
        return "fullpage-cache-" . md5(get_request_uri()
                        . getCurrentLanguage() . strbool(is_mobile())
                        . strbool(is_crawler()) . strbool(is_tablet()));
    }

    public static function clearAvatars(bool $removeDir = false): void {
        $path = Path::resolve("ULICMS_CONTENT/avatars");
        File::sureRemoveDir($path, $removeDir);
    }

}
