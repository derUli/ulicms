<?php
namespace UliCMS\HTML;

use ModuleHelper;

class Script
{

    public static function FromFile($file, $async = false, $defer = false, $htmlAttributes = array())
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
        foreach ($htmlAttributes as $key => $value) {
            $attributes[$key] = $value;
        }
        $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($attributes);
        
        return "<script $attribHTML></script>";
    }

    public static function FromString($code, $async = false, $defer = false, $htmlAttributes = array())
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