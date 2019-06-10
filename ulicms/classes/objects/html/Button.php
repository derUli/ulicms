<?php

namespace UliCMS\HTML;

use UliCMS\Constants\ButtonType;
use ModuleHelper;

class Button {

    public static function button($text, $type = ButtonType::BUTTON_SUBMIT, $htmlAttributes = [], $allowHtml = false) {
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

    public static function default($text, $type = ButtonType::BUTTON_SUBMIT, $htmlAttributes = [], $allowHtml = false) {
        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_DEFAULT;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    public static function primary($text, $type = ButtonType::BUTTON_SUBMIT, $htmlAttributes = [], $allowHtml = false) {
        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_PRIMARY;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    public static function success($text, $type = ButtonType::BUTTON_SUBMIT, $htmlAttributes = [], $allowHtml = false) {
        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_SUCCESS;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    public static function info($text, $type = ButtonType::BUTTON_SUBMIT, $htmlAttributes = [], $allowHtml = false) {
        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_INFO;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    public static function warning($text, $type = ButtonType::BUTTON_SUBMIT, $htmlAttributes = [], $allowHtml = false) {
        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_WARNING;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    public static function danger($text, $type = ButtonType::BUTTON_SUBMIT, $htmlAttributes = [], $allowHtml = false) {
        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_DANGER;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    public static function link($text, $type = ButtonType::BUTTON_SUBMIT, $htmlAttributes = [], $allowHtml = false) {
        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_LINK;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

}
