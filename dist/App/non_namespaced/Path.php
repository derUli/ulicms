<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

class Path {
    /**
     * Normalize path
     * @param string $path
     * @return string
     */
    public static function normalize(string $path): string {
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        return $path;
    }

    /**
     * Resolves a path including placeholder constants such as ULICMS_ROOT
     * @param string $path
     * @return string
     */
    public static function resolve(string $path): string {
        $path = str_ireplace('ULICMS_ROOT', ULICMS_ROOT, $path);

        $path = str_ireplace(
            'ULICMS_ROOT',
                ULICMS_ROOT,
            $path
        );
        $path = str_ireplace('ULICMS_CONFIGURATIONS', ULICMS_CONFIGURATIONS, $path);

        $path = str_ireplace('ULICMS_TMP', ULICMS_TMP, $path);
        $path = str_ireplace('ULICMS_CACHE_BASE', ULICMS_CACHE_BASE, $path);
        $path = str_ireplace('ULICMS_CACHE', ULICMS_CACHE, $path);
        $path = str_ireplace(
            'ULICMS_CONTENT',
            ULICMS_CONTENT,
            $path
        );
        $path = str_ireplace(
            'ULICMS_GENERATED_PRIVATE',
             ULICMS_GENERATED_PRIVATE,
            $path
        );

        $path = str_ireplace(
            'ULICMS_GENERATED_PUBLIC',
            ULICMS_GENERATED_PUBLIC,
            $path
        );
        $path = str_ireplace('ULICMS_LOG', ULICMS_LOG, $path);
        $path = str_ireplace('\\', '/', $path);
        $path = rtrim($path, '/');
        return $path;
    }
}
