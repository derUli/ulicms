<?php

declare(strict_types=1);

namespace UliCMS\HTML;

use ModuleHelper;
use UliCMS\Utils\File;

// generates HTML style tags
// please use stylesheet queue instead of this methods if possible
class Style
{
    public static function fromExternalFile(
        string $href,
        ?string $media = null,
        array $htmlAttributes = []
    ): string {
        $attributes = array(
            "rel" => "stylesheet",
            "href" => $href,
            "type" => "text/css"
        );
        if ($media) {
            $attributes["media"] = $media;
        }
        if (!parse_url($href, PHP_URL_SCHEME) && is_file($href)) {
            $attributes["href"] .= "?time=" . File::getLastChanged($href);
        }
        foreach ($htmlAttributes as $key => $value) {
            $attributes[$key] = $value;
        }
        $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($attributes);

        return "<link {$attribHTML}/>";
    }

    public static function fromString(
        ?string $code,
        ?string $media = null,
        array $htmlAttributes = []
    ): string {
        $attributes = array(
            "type" => "text/css"
        );
        if ($media) {
            $attributes["media"] = $media;
        }
        foreach ($htmlAttributes as $key => $value) {
            $attributes[$key] = $value;
        }
        $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($attributes);

        return "<style $attribHTML>" . $code . "</style>";
    }
}
