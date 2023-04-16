<?php

declare(strict_types=1);

namespace App\HTML;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

/**
 * This class contains helper methods to build Bootstrap alerts
 * See https://getbootstrap.com/docs/3.3/components/#alerts
 */
class Alert
{
    /**
     * Generate Bootstrap alert
     * @param string $text
     * @param string $cssClasses
     * @param bool $allowHtml
     * @return string
     */
    public static function alert(
        string $text,
        string $cssClasses = '',
        bool $allowHtml = false
    ): string {
        if (! $allowHtml) {
            $text = _esc($text);
        }
        return "<div class=\"alert {$cssClasses}\">{$text}</div>";
    }

    /**
     * Generate Bootstrap info style alert
     * @param string $text
     * @param string $cssClasses
     * @param bool $allowHtml
     * @return string
     */
    public static function info(
        string $text,
        string $cssClasses = '',
        bool $allowHtml = false
    ): string {
        return self::alert($text, "alert-info {$cssClasses}", $allowHtml);
    }

    /**
     * Generate Bootstrap danger style alert
     * @param type $text
     * @param string $cssClasses
     * @param bool $allowHtml
     * @return string
     */
    public static function danger(
        $text,
        string $cssClasses = '',
        bool $allowHtml = false
    ): string {
        return self::alert($text, "alert-danger {$cssClasses}", $allowHtml);
    }

    /**
     * Generate Bootstrap warning style alert
     * @param string $text
     * @param string $cssClasses
     * @param bool $allowHtml
     * @return string
     */
    public static function warning(
        string $text,
        string $cssClasses = '',
        bool $allowHtml = false
    ): string {
        return self::alert($text, "alert-warning {$cssClasses}", $allowHtml);
    }

    /**
     * Generate Bootstrap success style alert
     * @param string $text
     * @param string $cssClasses
     * @param bool $allowHtml
     * @return string
     */
    public static function success(
        string $text,
        string $cssClasses = '',
        bool $allowHtml = false
    ): string {
        return self::alert($text, "alert-success {$cssClasses}", $allowHtml);
    }
}
