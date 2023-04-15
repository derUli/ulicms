<?php

declare(strict_types=1);

namespace App\Storages;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

class Vars
{
    /**
     * @var array<string, mixed>
     */
    private static array $vars = [];

    /**
     * @var bool
     */
    private static bool $noCache = false;

    /**
     * Get a var
     *
     * @param string $var
     * @return mixed
     */
    public static function get(string $var): mixed
    {
        if (isset(self::$vars[$var])) {
            return self::$vars[$var];
        }
        return null;
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
        self::$vars[$var] = $val;
    }

    /**
     * Delete a var
     *
     * @param $var
     * @return void
     */
    public static function delete(string $var): void
    {
        if (isset(self::$vars[$var])) {
            unset(self::$vars[$var]);
        }
    }

    /**
     * Clear all vars
     *
     * @return void
     */
    public static function clear(): void
    {
        self::$vars = [];
    }

    /**
     * Get all vars
     *
     * @return array<string, mixed>
     */
    public static function getAllVars(): array
    {
        return self::$vars;
    }

    /**
     * Set no cache flag
     *
     * @param bool $bool
     * @return void
     */
    public static function setNoCache(bool $bool): void
    {
        self::$noCache = $bool;
    }

    /**
     * Get no cache flag
     *
     * @return bool
     */
    public static function getNoCache(): bool
    {
        return self::$noCache;
    }
}
