<?php

declare(strict_types=1);

namespace UliCMS\HTML;

use UliCMS\Utils\File;
use ModuleHelper;

// generates HTML script tags
// please use script queue instead of this methods if possible
class Script
{
    public static function fromFile(
        string $file,
        bool $async = false,
        bool $defer = false,
        array $htmlAttributes = []
    ): string
    {
        $attributes = array(
            "src" => $file,
            "type" => "text/javascript"
        );
        if ($async) {
            $attributes["async"] = "async";
        }
        if ($defer) {
            $attributes["defer"] = "defer";
        }
        if (!parse_url($file, PHP_URL_SCHEME) && is_file($file)) {
            $attributes["src"] .= "?time=" . File::getLastChanged($file);
        }
        foreach ($htmlAttributes as $key => $value) {
            $attributes[$key] = $value;
        }

        $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($attributes);

        return "<script $attribHTML></script>";
    }

    public static function fromString(
        ?string $code,
        bool $async = false,
        bool $defer = false,
        array $htmlAttributes = []
    ): string
    {
        $attributes = array(
            "type" => "text/javascript"
        );
        if ($async) {
            $attributes["async"] = "async";
        }
        if ($defer) {
            $attributes["defer"] = "defer";
        }
        foreach ($htmlAttributes as $key => $value) {
            $attributes[$key] = $value;
        }
        $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($attributes);

        return "<script $attribHTML>" . $code . "</script>";
    }
}
