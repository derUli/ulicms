<?php

declare(strict_types=1);

namespace UliCMS\Helpers;

class UrlHelper extends \Helper {

    // this method removes the get parameters string from $url
    // and returns the url without get parameters
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
