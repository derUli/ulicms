<?php

declare(strict_types=1);

namespace App\Security;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

class XSSProtection
{
    /**
     * Like PHP's strip_tags but remove also inline javascript code
     * @param string $input
     * @param string|null $allowed
     * @return string
     */
    public static function stripTags(string $input, ?string $allowed = null): string
    {
        $output = strip_tags($input, $allowed);

        // If <script> isn't allowed then remove also inline event handlers
        // such as onerror, onmouseover and onclik
        if (!($allowed && str_contains(strtolower($allowed), '<script>'))) {
            $output = preg_replace('/\bon\w+=\S+(?=.*>)/i', '', $output);
        }
        return $output;
    }
}
