<?php

declare(strict_types=1);

namespace App\Utils;

defined('ULICMS_ROOT') or exit('no direct script access allowed');

use Path;
use Settings;
use Phpfastcache\Helper\Psr16Adapter;
use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;
use ModuleManager;
use ControllerRegistry;
use DesignSettingsController;
use App\Helpers\TestHelper;

use function do_event;
use function sureRemoveDir;
use function get_request_uri;
use function getCurrentLanguage;
use function strbool;
use function is_mobile;
use function is_crawler;
use function is_tablet;

class CacheUtil
{
    private static $adapter;

    // returns a Psr16 cache adapter if caching is enabled
    // or $force is true
    // else returns null
    public static function getAdapter(bool $force = false): ?Psr16Adapter
    {
        if (!self::isCacheEnabled() && !$force) {
            return null;
        }

        if (self::$adapter !== null) {
            return self::$adapter;
        }

        $cacheConfig = array(
            "path" => Path::resolve("ULICMS_CACHE_BASE"),
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

    /**
     * Get the name of the Phpfastcache Driver
     * @return string
     */
    public static function getDriverName(): string
    {
        $driver = self::getBestMatchingDriver();

        return apply_filter($driver, 'cache_driver_name');
    }

    /**
     * Get best matching supported Phpfastcache driver
     * @return string
     */
    protected static function getBestMatchingDriver(): string
    {
        $driver = 'Devnull';

        $drivers = [
            'Memstatic' => TestHelper::isRunningPHPUnit(),
            // TODO: Prüfen, ob die Performance mit Apcu besser als mit Files ist
            // 'Apcu' => extension_loaded('apcu') && ini_get('apc.enabled'),
            'Files' => true,
                // 'Files' => CORE_COMPONENT !== CORE_COMPONENT_PHPUNIT
        ];

        foreach ($drivers as $name => $driverAvailable) {
            if ($driverAvailable) {
                $driver = $name;
                break;
            }
        }

        return $driver;
    }

    /**
     *  Resets cache adapter
     */
    public static function resetAdapater()
    {
        CacheManager::clearInstances();
        self::$adapter = null;
        self::getAdapter(true);
    }

    /**
     * Check if page cache is enabled
     * @return bool
     */
    public static function isCacheEnabled(): bool
    {
        return !Settings::get("cache_disabled") && !is_logged_in();
    }

    /**
     * Clear page cache
     * @return void
     */
    public static function clearPageCache(): void
    {
        $adapter = self::getAdapter();
        if ($adapter) {
            $adapter->clear();
        }
    }

    /**
     *  Clear general cache
     * @return void
     */
    public static function clearCache(): void
    {
        do_event("before_clear_cache");

        // clear opcache if available
        if (function_exists("opcache_reset")) {
            opcache_reset();
        }

        self::clearPageCache();

        sureRemoveDir(Path::resolve("ULICMS_CACHE"), false);
        sureRemoveDir(Path::resolve("ULICMS_TMP"), false);

        // Sync modules table in database with modules folder
        $moduleManager = new ModuleManager();
        $moduleManager->sync();

        $designSettingsController = ControllerRegistry::get(
            DesignSettingsController::class
        );
        $designSettingsController->_generateSCSSToFile();

        do_event("after_clear_cache");
    }

    /**
     * Get cache expiration
     * @return int
     */
    public static function getCachePeriod(): int
    {
        return (int) Settings::get("cache_period");
    }

    /**
     *
     * Get uid for current page
     * @return string
     */
    public static function getCurrentUid(): string
    {
        return "fullpage-cache-" . md5(get_request_uri()
                        . getCurrentLanguage() . strbool(is_mobile())
                        . strbool(is_crawler()) . strbool(is_tablet()));
    }

    /**
     * Clear generated avatars
     * @param bool $removeDir
     * @return void
     */
    public static function clearAvatars(bool $removeDir = false): void
    {
        $path = Path::resolve("ULICMS_CONTENT/avatars");
        File::sureRemoveDir($path, $removeDir);
    }
}