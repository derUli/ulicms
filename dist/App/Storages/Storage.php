<?php

declare(strict_types=1);

namespace App\Storages;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

abstract class Storage
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
    public static function getAll(): array
    {
        return self::$vars;
    }
}
