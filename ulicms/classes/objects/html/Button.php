<?php

declare(strict_types=1);

namespace UliCMS\HTML;

use UliCMS\Constants\ButtonType;
use ModuleHelper;

class Button {

    public static function button(string $text,
            string $type = ButtonType::BUTTON_SUBMIT,
            array $htmlAttributes = [],
            bool $allowHtml = false): string {
        if (!$allowHtml) {
            $text = _esc($text);
        }
        $htmlAttributes["type"] = $type;

        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_BASIC;
        }

        $attributesHtml = ModuleHelper::buildHTMLAttributesFromArray($htmlAttributes);
        return "<button {$attributesHtml}>{$text}</button>";
    }

    public static function default(string $text,
            string $type = ButtonType::BUTTON_SUBMIT,
            array $htmlAttributes = [], bool $allowHtml = false): string {
        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_DEFAULT;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    public static function primary(string $text,
            string $type = ButtonType::BUTTON_SUBMIT,
            array $htmlAttributes = [],
            bool $allowHtml = false): string {
        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_PRIMARY;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    public static function success(string $text,
            string $type = ButtonType::BUTTON_SUBMIT,
            array $htmlAttributes = [],
            bool $allowHtml = false) {
        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_SUCCESS;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    public static function info(string $text,
            string $type = ButtonType::BUTTON_SUBMIT,
            array $htmlAttributes = [],
            bool $allowHtml = false): string {
        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_INFO;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    public static function warning(string $text,
            string $type = ButtonType::BUTTON_SUBMIT,
            array $htmlAttributes = [],
            bool $allowHtml = false): string {
        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_WARNING;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    public static function danger(string $text,
            string $type = ButtonType::BUTTON_SUBMIT,
            array $htmlAttributes = [],
            bool $allowHtml = false): string {
        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_DANGER;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    public static function link(string $text,
            string $type = ButtonType::BUTTON_SUBMIT,
            array $htmlAttributes = [],
            bool $allowHtml = false): string {
        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_LINK;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

}
