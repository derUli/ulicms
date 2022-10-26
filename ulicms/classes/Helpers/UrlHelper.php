<?php

declare(strict_types=1);

namespace UliCMS\Helpers;

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

class UrlHelper extends \Helper {

    /**
     * Removes the get parameters string from $url
     * and returns the url without get parameters
     * @param string $url
     * @return string
     */
    public static function getUrlWithoutGetParameters(string $url): string {
        $parsedUri = parse_url($url);
        $hostWithPort = $parsedUri["host"];
        if (!empty($parsedUri["port"])) {
            $hostWithPort .= ":" . $parsedUri["port"];
        }

        $path = isset($parsedUri["path"]) ? $parsedUri["path"] : "";
        return $parsedUri["scheme"] . "://" . $hostWithPort
                . $path;
    }

}