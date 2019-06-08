<?php

namespace UliCMS\Utils;

use Path;
use Settings;
use Phpfastcache\Helper\Psr16Adapter;
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

class CacheUtil {

    private static $adapter;

    public static function getAdapter($force = false) {
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
        $driver = "files";
        if (extension_loaded("apcu") && ini_get("apc.enabled")) {
            $driver = "apcu";
        } else if (function_exists("sqlite_open")) {
            $driver = "sqlite";
        }

        self::$adapter = new Psr16Adapter($driver,
                new ConfigurationOption($cacheConfig));

        return self::$adapter;
    }

    public static function isCacheEnabled() {
        return (!Settings::get("cache_disabled") && !is_logged_in());
    }

    public static function clearPageCache() {
        $adapter = self::getAdapter();
        if ($adapter) {
            $adapter->clear();
        }
    }

    public static function clearCache() {
        do_event("before_clear_cache");

        // clear apc cache if available
        if (function_exists("apc_clear_cache")) {
            clearAPCCache();
        }
        // clear opcache if available
        if (function_exists("opcache_reset")) {
            opcache_reset();
        }

        $adapter = self::getAdapter();
        if ($adapter) {
            $adapter->clear();
        }

        sureRemoveDir(Path::resolve("ULICMS_CACHE"), false);

        // Sync modules table in database with modules folder
        $moduleManager = new ModuleManager();
        $moduleManager->sync();

        do_event("after_clear_cache");
    }

    // Return cache period in seconds
    public static function getCachePeriod() {
        return intval(Settings::get("cache_period"));
    }

    public static function getCurrentUid() {
        return "fullpage-cache-" . md5(get_request_uri() . getCurrentLanguage() . strbool(is_mobile()) . strbool(is_crawler()) . strbool(is_tablet()));
    }

}
