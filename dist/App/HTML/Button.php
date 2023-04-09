<?php

declare(strict_types=1);

namespace App\HTML;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

use App\Constants\ButtonType;
use ModuleHelper;

/**
 * This class contains helper methods to build Bootstrap buttons
 * See https://getbootstrap.com/docs/3.3/components/
 */
class Button
{
    /**
     * Generates Button
     * @param string $text
     * @param string $type
     * @param array $htmlAttributes
     * @param bool $allowHtml
     * @return string
     */
    public static function button(
        string $text,
        string $type = ButtonType::BUTTON_SUBMIT,
        array $htmlAttributes = [],
        bool $allowHtml = false
    ): string {
        if (!$allowHtml) {
            $text = _esc($text);
        }
        $htmlAttributes["type"] = $type;

        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_BASIC;
        }

        $attributesHtml = ModuleHelper::buildHTMLAttributesFromArray(
            $htmlAttributes
        );

        return "<button {$attributesHtml}>{$text}</button>";
    }

    /**
     * Generates default button
     * @param string $text
     * @param string $type
     * @param array $htmlAttributes
     * @param bool $allowHtml
     * @return string
     */
    public static function default(
        string $text,
        string $type = ButtonType::BUTTON_SUBMIT,
        array $htmlAttributes = [],
        bool $allowHtml = false
    ): string {
        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_DEFAULT;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    /**
     * Generates primary button
     * @param string $text
     * @param string $type
     * @param array $htmlAttributes
     * @param bool $allowHtml
     * @return string
     */
    public static function primary(
        string $text,
        string $type = ButtonType::BUTTON_SUBMIT,
        array $htmlAttributes = [],
        bool $allowHtml = false
    ): string {
        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_PRIMARY;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    /**
     * Generates success button
     * @param string $text
     * @param string $type
     * @param array $htmlAttributes
     * @param bool $allowHtml
     * @return type
     */
    public static function success(
        string $text,
        string $type = ButtonType::BUTTON_SUBMIT,
        array $htmlAttributes = [],
        bool $allowHtml = false
    ) {
        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_SUCCESS;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    /**
     * Generates info button
     * @param string $text
     * @param string $type
     * @param array $htmlAttributes
     * @param bool $allowHtml
     * @return string
     */
    public static function info(
        string $text,
        string $type = ButtonType::BUTTON_SUBMIT,
        array $htmlAttributes = [],
        bool $allowHtml = false
    ): string {
        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_INFO;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    /**
     * Generates warning button
     * @param string $text
     * @param string $type
     * @param array $htmlAttributes
     * @param bool $allowHtml
     * @return string
     */
    public static function warning(
        string $text,
        string $type = ButtonType::BUTTON_SUBMIT,
        array $htmlAttributes = [],
        bool $allowHtml = false
    ): string {
        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_WARNING;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    /**
     * Generates danger button
     * @param string $text
     * @param string $type
     * @param array $htmlAttributes
     * @param bool $allowHtml
     * @return string
     */
    public static function danger(
        string $text,
        string $type = ButtonType::BUTTON_SUBMIT,
        array $htmlAttributes = [],
        bool $allowHtml = false
    ): string {
        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_DANGER;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    /**
     * Generates link button
     * @param string $text
     * @param string $type
     * @param array $htmlAttributes
     * @param bool $allowHtml
     * @return string
     */
    public static function link(
        string $text,
        string $type = ButtonType::BUTTON_SUBMIT,
        array $htmlAttributes = [],
        bool $allowHtml = false
    ): string {
        if (!isset($htmlAttributes["class"])) {
            $htmlAttributes["class"] = ButtonType::TYPE_LINK;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }
}
