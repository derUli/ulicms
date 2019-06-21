<?php

namespace UliCMS\HTML;

use ModuleHelper;
use UliCMS\Utils\File;

class Style {

    public static function FromExternalFile($href, $media = null, $htmlAttributes = []) {
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

    public static function FromString($code, $media = null, $htmlAttributes = []) {
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
