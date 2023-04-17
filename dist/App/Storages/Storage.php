<?php

declare(strict_types=1);

namespace App\Storages;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Storages\Cached\MemstaticCached;

abstract class Storage extends MemstaticCached
{
    /**
     * @var array<string, mixed>
     */
    protected static array $vars = [];

    /**
     * Get a var
     *
     * @param string $var
     * @return mixed
     */
    public static function get(string $var): mixed
    {
        return static::getFromCache($var);
    }

    /**
     * Set a var
     *
     * @param string $var
     * @param mixed $val
     *
     * @return void
     */
    public static function set(string $var, mixed $val): void
    {
        static::setToCache($var, $val);
    }

    /**
     * Delete a var
     *
     * @param $var
     * @return void
     */
    public static function delete(string $var): void
    {
        static::deleteFromCache($var);
    }

    /**
     * Clear all vars
     *
     * @return void
     */
    public static function clear(): void
    {
        static::clearCache();
    }
}
