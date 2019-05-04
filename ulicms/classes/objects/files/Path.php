<?php

class Path {

    public static function resolve($path) {
        $path = str_ireplace("ULICMS_ROOT", rtrim(ULICMS_ROOT, "/"), $path);
        $path = str_ireplace("ULICMS_DATA_STORAGE_ROOT", rtrim(ULICMS_DATA_STORAGE_ROOT, "/"), $path);
        $path = str_ireplace("ULICMS_CONFIGURATIONS", ULICMS_CONFIGURATIONS, $path);
        if (defined("ULICMS_DATA_STORAGE_URL")) {
            $path = str_ireplace("ULICMS_DATA_STORAGE_URL", rtrim(ULICMS_DATA_STORAGE_URL, "/"), $path);
        }
        $path = str_ireplace("ULICMS_TMP", rtrim(ULICMS_TMP, "/"), $path);
        $path = str_ireplace("ULICMS_CACHE", rtrim(ULICMS_CACHE, "/"), $path);
        $path = str_ireplace("ULICMS_LOG", rtrim(ULICMS_LOG, "/"), $path);
        $path = str_ireplace("\\", "/", $path);
        $path = rtrim($path, "/");
        return $path;
    }

    public static function removeDir($dir, $deleteMe) {
        File::sureRemoveDir($dir, $deleteMe);
    }

}
