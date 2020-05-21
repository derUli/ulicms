<?php

declare(strict_types=1);

class Path {

    // resolves a path including placeholder constants such as ULICMS_ROOT
    public static function resolve(string $path): string {
        $path = str_ireplace("ULICMS_ROOT", rtrim(ULICMS_ROOT, "/"), $path);
        
        $path = str_ireplace(
                "ULICMS_DATA_STORAGE_ROOT",
                rtrim(ULICMS_DATA_STORAGE_ROOT, "/"
                ), $path
        );
        $path = str_ireplace("ULICMS_CONFIGURATIONS", ULICMS_CONFIGURATIONS, $path);
        if (defined("ULICMS_DATA_STORAGE_URL")) {
            $path = str_ireplace(
                    "ULICMS_DATA_STORAGE_URL",
                    rtrim(ULICMS_DATA_STORAGE_URL, "/"
                    ), $path
            );
        }
        $path = str_ireplace("ULICMS_TMP", rtrim(ULICMS_TMP, "/"), $path);
        $path = str_ireplace("ULICMS_CACHE", rtrim(ULICMS_CACHE, "/"), $path);
        $path = str_ireplace(
                "ULICMS_CONTENT",
                rtrim(ULICMS_CONTENT, "/"
                ), $path
        );
        $path = str_ireplace(
                "ULICMS_GENERATED",
                rtrim(ULICMS_GENERATED, "/"
                ), $path
        );
        $path = str_ireplace("ULICMS_LOG", rtrim(ULICMS_LOG, "/"), $path);
        $path = str_ireplace("\\", "/", $path);
        $path = rtrim($path, "/");
        return $path;
    }

}
