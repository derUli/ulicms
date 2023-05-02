<?php

declare(strict_types=1);

namespace App\HTML;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Utils\File;
use ModuleHelper;

/**
 * Utils to generate HTML <script> Tags
 */
class Script {
    /**
     * Generates a <script> tag for file
     *
     * @param string $file
     * @param bool $async
     * @param bool $defer
     * @param array<string, string> $htmlAttributes
     *
     * @return string
     */
    public static function fromFile(
        string $file,
        bool $async = false,
        bool $defer = false,
        array $htmlAttributes = []
    ): string {
        $attributes = [
            'src' => $file
        ];

        if ($async) {
            $attributes['async'] = 'async';
        }

        if ($defer) {
            $attributes['defer'] = 'defer';
        }

        if (! parse_url($file, PHP_URL_SCHEME) && is_file($file)) {
            $attributes['src'] .= '?time=' . File::getLastChanged($file);
        }

        foreach ($htmlAttributes as $key => $value) {
            $attributes[$key] = $value;
        }

        $attribHTML = \App\Helpers\ModuleHelper::buildHTMLAttributesFromArray($attributes);

        if (! empty($attribHTML)) {
            $attribHTML = ' ' . $attribHTML;
        }

        return "<script{$attribHTML}></script>";
    }

    /**
     * Generates a inline <script> tag
     *
     * @param string|null $code
     * @param bool $async
     * @param bool $defer
     * @param array<string, string> $htmlAttributes
     *
     * @return string
     */
    public static function fromString(
        ?string $code,
        bool $async = false,
        bool $defer = false,
        array $htmlAttributes = []
    ): string {
        $attributes = [];
        if ($async) {
            $attributes['async'] = 'async';
        }
        if ($defer) {
            $attributes['defer'] = 'defer';
        }
        foreach ($htmlAttributes as $key => $value) {
            $attributes[$key] = $value;
        }
        $attribHTML = \App\Helpers\ModuleHelper::buildHTMLAttributesFromArray($attributes);

        if (! empty($attribHTML)) {
            $attribHTML = ' ' . $attribHTML;
        }

        return "<script{$attribHTML}>" . $code . '</script>';
    }
}
