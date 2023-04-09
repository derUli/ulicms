<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('no direct script access allowed');

class Path
{
    /**
     * Normalize path
     * @param string $path
     * @return string
     */
    public static function normalize(string $path): string
    {
        $path = str_replace("\\", DIRECTORY_SEPARATOR, $path);
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        return $path;
    }

    /**
     * Resolves a path including placeholder constants such as ULICMS_ROOT
     * @param string $path
     * @return string
     */
    public static function resolve(string $path): string
    {
        $path = str_ireplace("ULICMS_ROOT", rtrim(ULICMS_ROOT, '/'), $path);

        $path = str_ireplace(
            "ULICMS_ROOT",
            rtrim(
                ULICMS_ROOT,
                '/'
            ),
            $path
        );
        $path = str_ireplace("ULICMS_CONFIGURATIONS", ULICMS_CONFIGURATIONS, $path);

        $path = str_ireplace("ULICMS_TMP", rtrim(ULICMS_TMP, '/'), $path);
        $path = str_ireplace("ULICMS_CACHE_BASE", rtrim(ULICMS_CACHE_BASE, '/'), $path);
        $path = str_ireplace("ULICMS_CACHE", rtrim(ULICMS_CACHE, '/'), $path);
        $path = str_ireplace(
            "ULICMS_CONTENT",
            rtrim(
                ULICMS_CONTENT,
                '/'
            ),
            $path
        );
        $path = str_ireplace(
            "ULICMS_GENERATED",
            rtrim(
                ULICMS_GENERATED,
                '/'
            ),
            $path
        );
        $path = str_ireplace("ULICMS_LOG", rtrim(ULICMS_LOG, '/'), $path);
        $path = str_ireplace("\\", '/', $path);
        $path = rtrim($path, '/');
        return $path;
    }
}
