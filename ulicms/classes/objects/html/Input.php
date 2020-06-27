<?php

declare(strict_types=1);

namespace UliCMS\HTML;

use Path;
use ModuleHelper;
use Template;

// this class contains methods to build input fields
class Input
{
    public static function textBox(
        string $name,
        $value,
        string $type = "text",
        array $htmlAttributes = []
    ): string
    {
        $attributes = array(
            "type" => $type,
            "name" => $name,
            "value" => $value
        );
        foreach ($htmlAttributes as $key => $val) {
            $attributes[$key] = $val;
        }
        $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($attributes);
        return "<input {$attribHTML}>";
    }

    public static function textArea(
        string $name,
        $value,
        int $rows = 25,
        int $cols = 80,
        array $htmlAttributes = []
    ): string
    {
        $attributes = array(
            "name" => $name,
            "rows" => $rows,
            "cols" => $cols
        );
        foreach ($htmlAttributes as $key => $val) {
            $attributes[$key] = $val;
        }
        $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($attributes);

        $escapedValue = Template::getEscape($value);

        return "<textarea {$attribHTML}>{$escapedValue}</textarea>";
    }

    public static function editor(
        string $name,
        $value,
        int $rows = 25,
        int $cols = 80,
        array $htmlAttributes = []
    ): string
    {
        if (!isset($htmlAttributes["id"])) {
            $htmlAttributes["id"] = $name;
        }
        if (isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] .= " " . get_html_editor();
        } else {
            $htmlAttributes["class"] = get_html_editor();
        }

        $htmlAttributes["class"] = trim($htmlAttributes["class"]);

        $htmlAttributes["data-mimetype"] = !isset($htmlAttributes["data-mimetype"]) ?
                "text/html" : $htmlAttributes["data-mimetype"];

        return self::textArea($name, $value, $rows, $cols, $htmlAttributes);
    }

    /*

     * 	<textarea name="content" id="content" cols=60 rows=20 class="<?php esc($editor); ?>" data-mimetype="text/html"
     *      */

    public static function password(
        string $name,
        $value,
        array $htmlAttributes = []
    ): string
    {
        return self::textBox($name, $value, "password", $htmlAttributes);
    }

    public static function file(
        string $name,
        bool $multiple = false,
        $accept = null,
        array $htmlAttributes = []
    ): string
    {
        $attributes = array(
            "name" => $name
        );
        if (is_string($accept)) {
            $attributes["accept"] = Template::getEscape($accept);
        } elseif (is_array($accept)) {
            $accept = join(", ", $accept);
            $attributes["accept"] = Template::getEscape($accept);
        }
        if ($multiple) {
            $attributes["multiple"] = "multiple";
        }

        foreach ($htmlAttributes as $key => $val) {
            $attributes[$key] = $val;
        }

        return self::textBox($name, "", "file", $attributes);
    }

    public static function hidden(
        string $name,
        $value,
        array $htmlAttributes = []
    ): string
    {
        return self::textBox($name, $value, "hidden", $htmlAttributes);
    }

    public static function checkBox(
        string $name,
        bool $checked = false,
        $value = "1",
        array $htmlAttributes = []
    ): string
    {
        if ($checked) {
            $htmlAttributes["checked"] = "checked";
        }
        return self::textBox($name, $value, "checkbox", $htmlAttributes);
    }

    public static function radioButton(
        string $name,
        bool $checked = false,
        $value = "1",
        array $htmlAttributes = []
    ): string
    {
        if ($checked) {
            $htmlAttributes["checked"] = "checked";
        }
        return self::textBox($name, $value, "radio", $htmlAttributes);
    }

    public static function singleSelect(
        string $name,
        $value = null,
        array $options = [],
        int $size = 1,
        array $htmlAttributes = []
    ): string
    {
        $attributes = array(
            "name" => $name,
            "size" => $size
        );
        foreach ($htmlAttributes as $key => $val) {
            $attributes[$key] = $val;
        }
        
        if (!isset($attributes["id"])) {
            $attributes["id"] = $name;
        }
        
        $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($attributes);

        $html = "<select $attribHTML>";
        foreach ($options as $option) {
            if ($value == $option->getValue()) {
                $option->setSelected(true);
            }
            $html .= $option;
        }
        $html .= '</select>';
        return $html;
    }

    public static function multiSelect(
        string $name,
        $value = null,
        array $options = [],
        int $size = 5,
        array $htmlAttributes = []
    ): string
    {
        $attributes = array(
            "name" => $name,
            "size" => $size
        );
        foreach ($htmlAttributes as $key => $val) {
            $attributes[$key] = $val;
        }
        $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($attributes);

        $html = "<select $attribHTML multiple>";
        foreach ($options as $option) {
            if (is_array($value) and in_array($option->getValue(), $value)) {
                $option->setSelected(true);
            }
            $html .= $option;
        }
        $html .= '</select>';
        return $html;
    }
}
