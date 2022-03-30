<?php

declare(strict_types=1);

namespace UliCMS\Security;

class XSSProtection {

    // like PHP's strip_tags
    // But remove also inline javascript code
    public static function stripTags(string $input, ?string $allowed = null): string {
        $output = strip_tags($input, $allowed);

        // if <script> isn't allowed then remove also inline event handlers
        // such as onerror, onmouseover and onclik
        if (!($allowed and str_contains(strtolower($allowed), "<script>"))) {
            $output = preg_replace('/\bon\w+=\S+(?=.*>)/i', "", $output);
        }
        return $output;
    }

}
