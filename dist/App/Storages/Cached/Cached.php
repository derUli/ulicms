<?php

namespace App\Storages\Cached;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Security\Hash;
use Phpfastcache\Helper\Psr16Adapter;

abstract class Cached
{
    /**
     * @var Psr16Adapter $adapter
     */
    protected static ?Psr16Adapter $adapter = null;

    /**
     * Retrieve existing setting from cache
     * @param string $key
     * @return mixed
     */
    protected static function retrieveFromCache(string $key): mixed
    {
        $adapter = static::getCacheAdapter();
        $cacheUid = static::generateCacheUid($key);
        return $adapter->get($cacheUid);
    }

    /**
     * Store setting in cache
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    protected static function storeInCache(string $key, mixed $value): bool
    {
        $adapter = static::getCacheAdapter();
        $cacheUid = static::generateCacheUid($key);

        return $adapter->set($cacheUid, $value);
    }

    /**
     * Delete setting from cache
     * @param string $key
     * @return bool
     */
    protected static function deleteInCache(string $key): bool
    {
        $adapter = static::getCacheAdapter();
        $cacheUid = static::generateCacheUid($key);
        return $adapter->delete($cacheUid);
    }

    /**
     * Generate Cache uid from settings name
     * @param string $key
     * @return string
     */
    protected static function generateCacheUid(string $key)
    {
        return Hash::hashCacheIdentifier($key);
    }

    /**
     * Get caching adapter
     * @return Psr16Adapter
     */
    abstract protected static function getCacheAdapter(): Psr16Adapter;
}
