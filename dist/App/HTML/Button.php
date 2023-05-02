<?php

declare(strict_types=1);

namespace App\HTML;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Constants\ButtonType;
use ModuleHelper;

/**
 * This class contains helper methods to build Bootstrap buttons
 * See https://getbootstrap.com/docs/3.3/components/
 */
class Button {
    /**
     * Generate Button
     *
     * @param string $text
     * @param string $type
     * @param array<string, string> $htmlAttributes
     *
     * @param bool $allowHtml
     *
     * @return string
     */
    public static function button(
        string $text,
        string $type = ButtonType::BUTTON_SUBMIT,
        array $htmlAttributes = [],
        bool $allowHtml = false
    ): string {
        if (! $allowHtml) {
            $text = _esc($text);
        }
        $htmlAttributes['type'] = $type;

        if (! isset($htmlAttributes['class'])) {
            $htmlAttributes['class'] = ButtonType::TYPE_BASIC;
        }

        $attributesHtml = \App\Helpers\ModuleHelper::buildHTMLAttributesFromArray(
            $htmlAttributes
        );

        return "<button {$attributesHtml}>{$text}</button>";
    }

    /**
     * Generate default button
     *
     * @param string $text
     * @param string $type
     * @param array<string, string> $htmlAttributes
     *
     * @param bool $allowHtml
     *
     * @return string
     */
    public static function default(
        string $text,
        string $type = ButtonType::BUTTON_SUBMIT,
        array $htmlAttributes = [],
        bool $allowHtml = false
    ): string {
        if (! isset($htmlAttributes['class'])) {
            $htmlAttributes['class'] = ButtonType::TYPE_DEFAULT;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    /**
     * Generate primary button
     *
     * @param string $text
     * @param string $type
     * @param array<string, string> $htmlAttributes
     * @param bool $allowHtml
     *
     * @return string
     */
    public static function primary(
        string $text,
        string $type = ButtonType::BUTTON_SUBMIT,
        array $htmlAttributes = [],
        bool $allowHtml = false
    ): string {
        if (! isset($htmlAttributes['class'])) {
            $htmlAttributes['class'] = ButtonType::TYPE_PRIMARY;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    /**
     * Generate success button
     *
     * @param string $text
     * @param string $type
     * @param array<string, string> $htmlAttributes
     * @param bool $allowHtml
     *
     * @return string
     */
    public static function success(
        string $text,
        string $type = ButtonType::BUTTON_SUBMIT,
        array $htmlAttributes = [],
        bool $allowHtml = false
    ) {
        if (! isset($htmlAttributes['class'])) {
            $htmlAttributes['class'] = ButtonType::TYPE_SUCCESS;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    /**
     * Generate info button
     *
     * @param string $text
     * @param string $type
     * @param array<string, string> $htmlAttributes
     * @param bool $allowHtml
     *
     * @return string
     */
    public static function info(
        string $text,
        string $type = ButtonType::BUTTON_SUBMIT,
        array $htmlAttributes = [],
        bool $allowHtml = false
    ): string {
        if (! isset($htmlAttributes['class'])) {
            $htmlAttributes['class'] = ButtonType::TYPE_INFO;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    /**
     * Generate warning button
     *
     * @param string $text
     * @param string $type
     * @param array<string, string> $htmlAttributes
     * @param bool $allowHtml
     *
     * @return string
     */
    public static function warning(
        string $text,
        string $type = ButtonType::BUTTON_SUBMIT,
        array $htmlAttributes = [],
        bool $allowHtml = false
    ): string {
        if (! isset($htmlAttributes['class'])) {
            $htmlAttributes['class'] = ButtonType::TYPE_WARNING;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    /**
     * Generate danger button
     *
     * @param string $text
     * @param string $type
     * @param array<string, string> $htmlAttributes
     * @param bool $allowHtml
     *
     * @return string
     */
    public static function danger(
        string $text,
        string $type = ButtonType::BUTTON_SUBMIT,
        array $htmlAttributes = [],
        bool $allowHtml = false
    ): string {
        if (! isset($htmlAttributes['class'])) {
            $htmlAttributes['class'] = ButtonType::TYPE_DANGER;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }

    /**
     * Generate link button
     *
     * @param string $text
     * @param string $type
     * @param array<string, string> $htmlAttributes
     * @param bool $allowHtml
     *
     * @return string
     */
    public static function link(
        string $text,
        string $type = ButtonType::BUTTON_SUBMIT,
        array $htmlAttributes = [],
        bool $allowHtml = false
    ): string {
        if (! isset($htmlAttributes['class'])) {
            $htmlAttributes['class'] = ButtonType::TYPE_LINK;
        }

        return self::button($text, $type, $htmlAttributes, $allowHtml);
    }
}
