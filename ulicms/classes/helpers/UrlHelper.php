<?php

namespace UliCMS\Helpers;

class UrlHelper extends \Helper {

    public static function getUrlWithoutGetParameters($url) {
        $parsedUri = parse_url($url);
        $hostWithPort = $parsedUri["host"];
        if (!empty($parsedUri["port"])) {
            $hostWithPort .= ":" . $parsedUri["port"];
        }
        return $parsedUri["scheme"] . "://" . $hostWithPort . $parsedUri["path"];
    }

}
