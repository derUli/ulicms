<?php

namespace App\Storages;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

interface StorageInterface {
    public static function get(string $var): mixed;

    /**
     * Set a var
     *
     * @param string $var
     * @param mixed $val
     *
     * @return void
     */
    public static function set(string $var, mixed $val): void;

    /**
     * Delete a var
     *
     * @param $var
     * @return void
     */
    public static function delete(string $var): void;

    /**
     * Clear all vars
     *
     * @return void
     */
    public static function clear(): void ;
}
