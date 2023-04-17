<?php

namespace App\Storages\Cached;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use Phpfastcache\Config\ConfigurationOption;
use Phpfastcache\Helper\Psr16Adapter;

abstract class MemstaticCached extends Cached
{
    /**
     * Get caching adapter
     * @return Psr16Adapter
     */
    protected static function getCacheAdapter(): Psr16Adapter
    {
        if (static::$adapter) {
            return static::$adapter;
        }

        $cacheConfig = [
            'defaultTtl' => ONE_DAY_IN_SECONDS,
        ];

        // Use a Memstatic adapter, because persistent caching would worse
        // performance instead of improving it
        static::$adapter = new Psr16Adapter(
            'Memstatic',
            new ConfigurationOption($cacheConfig)
        );

        return static::$adapter;
    }
}
