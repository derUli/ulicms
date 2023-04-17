<?php

declare(strict_types=1);

namespace App\Helpers;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

/**
 * Helper methods for handling URLs
 */
abstract class UrlHelper extends Helper
{
    /**
     * This method removes the get parameters from an URL
     * @param string $url
     * @return string
     */
    public static function getUrlWithoutGetParameters(string $url): string
    {
        $parsedUri = parse_url($url);
        $hostWithPort = $parsedUri['host'];
        if (! empty($parsedUri['port'])) {
            $hostWithPort .= ':' . $parsedUri['port'];
        }

        $path = $parsedUri['path'] ?? '';
        return $parsedUri['scheme'] . '://' . $hostWithPort
                . $path;
    }
}
