<?php

declare(strict_types=1);

namespace App\HTML;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use Template;

/**
 * This class contains methods to build input fields
 */
class Input {
    /**
     * Generate text input
     *
     * @param string $name
     * @param mixed $value
     * @param string $type
     * @param array <string, string> $htmlAttributes
     *
     * @return string
     */
    public static function textBox(
        string $name,
        mixed $value,
        string $type = 'text',
        array $htmlAttributes = []
    ): string {
        $attributes = [
            'type' => $type,
            'name' => $name,
            'value' => $value
        ];
        foreach ($htmlAttributes as $key => $val) {
            $attributes[$key] = $val;
        }
        $attribHTML = \App\Helpers\ModuleHelper::buildHTMLAttributesFromArray($attributes);
        return "<input {$attribHTML}>";
    }

    /**
     * Generate textarea input
     *
     * @param string $name
     * @param mixed $value
     * @param int $rows
     * @param int $cols
     * @param array <string, string> $htmlAttributes
     *
     * @return string
     */
    public static function textArea(
        string $name,
        mixed $value,
        int $rows = 25,
        int $cols = 80,
        array $htmlAttributes = []
    ): string {
        $attributes = [
            'name' => $name,
            'rows' => $rows,
            'cols' => $cols
        ];
        foreach ($htmlAttributes as $key => $val) {
            $attributes[$key] = $val;
        }
        $attribHTML = \App\Helpers\ModuleHelper::buildHTMLAttributesFromArray($attributes);

        $escapedValue = Template::getEscape($value);

        return "<textarea {$attribHTML}>{$escapedValue}</textarea>";
    }

    /**
     * Generate textarea with HTML editor
     *
     * @param string $name
     * @param mixed $value
     * @param int $rows
     * @param int $cols
     * @param array <string, string> $htmlAttributes
     *
     * @return string
     */
    public static function editor(
        string $name,
        mixed $value,
        int $rows = 25,
        int $cols = 80,
        array $htmlAttributes = []
    ): string {
        if (! isset($htmlAttributes['id'])) {
            $htmlAttributes['id'] = $name;
        }
        if (isset($htmlAttributes['class'])) {
            $htmlAttributes['class'] .= ' ' . get_html_editor();
        } else {
            $htmlAttributes['class'] = get_html_editor();
        }

        $htmlAttributes['class'] = trim($htmlAttributes['class']);

        $htmlAttributes['data-mimetype'] = ! isset($htmlAttributes['data-mimetype']) ?
                'text/html' : $htmlAttributes['data-mimetype'];

        return self::textArea($name, $value, $rows, $cols, $htmlAttributes);
    }

    /**
     * Generate password input
     *
     * @param string $name
     * @param mixed $value
     * @param array <string, string> $htmlAttributes
     *
     * @return string
     */
    public static function password(
        string $name,
        mixed $value,
        array $htmlAttributes = []
    ): string {
        return self::textBox($name, $value, 'password', $htmlAttributes);
    }

    /**
     * Generate file input
     *
     * @param string $name
     * @param bool $multiple
     * @param string|string[]|null $accept
     * @param array <string, string> $htmlAttributes
     *
     * @return string
     */
    public static function file(
        string $name,
        bool $multiple = false,
        $accept = null,
        array $htmlAttributes = []
    ): string {
        $attributes = [
            'name' => $name
        ];
        if (is_string($accept)) {
            $attributes['accept'] = Template::getEscape($accept);
        } elseif (is_array($accept)) {
            $accept = implode(', ', $accept);
            $attributes['accept'] = Template::getEscape($accept);
        }
        if ($multiple) {
            $attributes['multiple'] = 'multiple';
        }

        foreach ($htmlAttributes as $key => $val) {
            $attributes[$key] = $val;
        }

        return self::textBox($name, '', 'file', $attributes);
    }

    /**
     * Generate hidden input
     *
     * @param string $name
     * @param mixed $value
     * @param array <string, string> $htmlAttributes
     *
     * @return string
     */
    public static function hidden(
        string $name,
        $value,
        array $htmlAttributes = []
    ): string {
        return self::textBox($name, $value, 'hidden', $htmlAttributes);
    }

    /**
     * Generate checkbox input
     *
     * @param string $name
     * @param bool $checked
     * @param mixed $value
     * @param array <string, string> $htmlAttributes
     *
     * @return string
     */
    public static function checkBox(
        string $name,
        bool $checked = false,
        $value = '1',
        array $htmlAttributes = []
    ): string {
        if ($checked) {
            $htmlAttributes['checked'] = 'checked';
        }
        return self::textBox($name, $value, 'checkbox', $htmlAttributes);
    }

    /**
     * Generate radio button
     *
     * @param string $name
     * @param bool $checked
     * @param mixed $value
     * @param array <string, string> $htmlAttributes
     *
     * @return string
     */
    public static function radioButton(
        string $name,
        bool $checked = false,
        $value = '1',
        array $htmlAttributes = []
    ): string {
        if ($checked) {
            $htmlAttributes['checked'] = 'checked';
        }
        return self::textBox($name, $value, 'radio', $htmlAttributes);
    }

    /**
     * Generate single select
     *
     * @param string $name
     * @param mixed $value
     * @param ListItem[] $options
     * @param int $size
     * @param array <string, string> $htmlAttributes
     *
     * @return string
     */
    public static function singleSelect(
        string $name,
        $value = null,
        array $options = [],
        int $size = 1,
        array $htmlAttributes = []
    ): string {
        $attributes = [
            'name' => $name,
            'size' => $size
        ];
        foreach ($htmlAttributes as $key => $val) {
            $attributes[$key] = $val;
        }

        if (! isset($attributes['id'])) {
            $attributes['id'] = $name;
        }

        $attribHTML = \App\Helpers\ModuleHelper::buildHTMLAttributesFromArray($attributes);

        $html = "<select {$attribHTML}>";
        foreach ($options as $option) {
            if ($value == $option->getValue()) {
                $option->setSelected(true);
            }
            $html .= $option;
        }
        $html .= '</select>';
        return $html;
    }

    /**
     * Generate multiselect
     *
     * @param string $name
     * @param mixed $value
     * @param ListItem[] $options
     * @param int $size
     * @param array <string, string> $htmlAttributes
     *
     * @return string
     */
    public static function multiSelect(
        string $name,
        mixed $value = null,
        array $options = [],
        int $size = 5,
        array $htmlAttributes = []
    ): string {
        $attributes = [
            'name' => $name,
            'size' => $size
        ];
        foreach ($htmlAttributes as $key => $val) {
            $attributes[$key] = $val;
        }
        $attribHTML = \App\Helpers\ModuleHelper::buildHTMLAttributesFromArray($attributes);

        $html = "<select {$attribHTML} multiple>";
        foreach ($options as $option) {
            if (is_array($value) && in_array($option->getValue(), $value)) {
                $option->setSelected(true);
            }
            $html .= $option;
        }
        $html .= '</select>';
        return $html;
    }
}
