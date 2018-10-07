<?php
namespace UliCMS\HTML;

use ModuleHelper;

class Style
{

    public static function FromExternalFile($href, $media = null, $htmlAttributes = array())
    {
        $attributes = array(
            "rel" => "stylesheet",
            "href" => $href,
            "type" => "text/css"
        );
        if ($media) {
            $attributes["media"] = $media;
        }
        foreach ($htmlAttributes as $key => $value) {
            $attributes[$key] = $value;
        }
        $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($attributes);
        
        return "<link {$attribHTML}/>";
    }

    public static function FromString($code, $media = null, $htmlAttributes = array())
    {
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

