<?php

declare(strict_types=1);

namespace App\Storages;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

class ViewBag
{
    /**
     * @var array<string, mixed> $vars
     */
    private static array $vars = [];

    /**
     * Get var
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
     * Set var
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
     * Delete var
     *
     * @param string $var
     * @return void
     */
    public static function delete(string $var): void
    {
        if (isset(self::$vars[$var])) {
            unset(self::$vars[$var]);
        }
    }

    /**
     * Clear vars
     *
     * @return void
     */
    public static function clear(): void
    {
        self::$vars = [];
    }

    /**
     *
     * @return array<string, mixed>
     **/
    public static function getAllVars(): array
    {
        return self::$vars;
    }
}
