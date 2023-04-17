<?php

namespace App\Utils;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Security\Hash;
use Phpfastcache\Config\ConfigurationOption;
use Phpfastcache\Helper\Psr16Adapter;

abstract class MemstaticCached
{
    private static $adapter;

    /**
     * Retrieve existing setting from cache
     * @param string $key
     * @return mixed
     */
    protected static function retrieveFromCache(string $key): mixed
    {
        $adapter = self::getCacheAdapter();
        $cacheUid = self::generateCacheUid($key);
        return $adapter->get($cacheUid);
    }

    /**
     * Store setting in cache
     * @param string $key
     * @param type $value
     * @return bool
     */
    protected static function storeInCache(string $key, $value): bool
    {
        $adapter = self::getCacheAdapter();
        $cacheUid = self::generateCacheUid($key);

        return $adapter->set($cacheUid, $value);
    }

    /**
     * Delete setting from cache
     * @param string $key
     * @return bool
     */
    protected static function deleteInCache(string $key): bool
    {
        $adapter = self::getCacheAdapter();
        $cacheUid = self::generateCacheUid($key);
        return $adapter->delete($cacheUid);
    }

    /**
     * Generate Cache uid from settings name
     * @param type $key
     * @return type
     */
    protected static function generateCacheUid($key)
    {
        return Hash::hashCacheIdentifier($key);
    }

    /**
     * Get caching adapter
     * @return Psr16Adapter
     */
    protected static function getCacheAdapter(): Psr16Adapter
    {
        if (self::$adapter) {
            return self::$adapter;
        }

        $cacheConfig = [
            'defaultTtl' => ONE_DAY_IN_SECONDS,
        ];

        // Use a Memstatic adapter, because persistent caching would worse
        // performance instead of improving it
        self::$adapter = new Psr16Adapter(
            'Memstatic',
            new ConfigurationOption($cacheConfig)
        );

        return self::$adapter;
    }
}
