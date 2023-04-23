<?php

declare(strict_types=1);

namespace App\HTML;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Utils\File;
use ModuleHelper;

/**
 * Utils to generate <style> tags
 */
class Style {
    /**
     * Generates a <link> tag for a stylesheet file
     *
     * @param string $href
     * @param ?string $media
     * @param array<string, string> $htmlAttributes
     *
     * @return string
     */
    public static function fromExternalFile(
        string $href,
        ?string $media = null,
        array $htmlAttributes = []
    ): string {
        $attributes = [
            'rel' => 'stylesheet',
            'href' => $href,
            'type' => 'text/css',
        ];
        if ($media) {
            $attributes['media'] = $media;
        }
        if (! parse_url($href, PHP_URL_SCHEME) && is_file($href)) {
            $attributes['href'] .= '?time=' . File::getLastChanged($href);
        }
        foreach ($htmlAttributes as $key => $value) {
            $attributes[$key] = $value;
        }
        $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($attributes);

        if (! empty($attribHTML)) {
            $attribHTML = ' ' . $attribHTML;
        }

        return "<link{$attribHTML}/>";
    }

    /**
     * Generates a <script> tag from string
     *
     * @param string|null $code
     * @param string|null $media
     * @param array<string, string> $htmlAttributes
     *
     * @return string
     */
    public static function fromString(
        ?string $code,
        ?string $media = null,
        array $htmlAttributes = []
    ): string {
        $attributes = [];
        if ($media) {
            $attributes['media'] = $media;
        }
        foreach ($htmlAttributes as $key => $value) {
            $attributes[$key] = $value;
        }
        $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($attributes);

        if (! empty($attribHTML)) {
            $attribHTML = ' ' . $attribHTML;
        }

        return "<style{$attribHTML}>" . $code . '</style>';
    }
}
