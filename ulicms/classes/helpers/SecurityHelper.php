<?php

class SecurityHelper extends Helper {

    public static function securePath($path) {
        $securedPath = array();
        $path = explode("/", $path);
        foreach ($path as $key => $value) {
            if ($value != "." and $value != "..") {
                $securedPath[] = $value;
            }
        }
        $securedPath = array_map('trim', $securedPath);
        $securedPath = array_filter($securedPath, 'strlen');
        $securedPath = array_filter($securedPath, 'is_null');
        $securedPath = "/" . implode("/", $securedPath);
        return $securedPath;
    }

}
