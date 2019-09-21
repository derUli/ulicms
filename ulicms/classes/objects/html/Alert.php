<?php

declare(strict_types=1);

namespace UliCMS\HTML;

// This class contains helper methods to build Bootstrap alerts
// see https://getbootstrap.com/docs/3.3/components/
class Alert {

    public static function alert(
            string $text,
            string $cssClasses,
            bool $allowHtml = false
    ): string {
        if (!$allowHtml) {
            $text = _esc($text);
        }
        return "<div class=\"alert {$cssClasses}\">{$text}</div>";
    }

    public static function info(
            string $text,
            string $cssClasses = "",
            bool $allowHtml = false
    ): string {
        return self::alert($text, "alert-info {$cssClasses}", $allowHtml);
    }

    public static function danger(
            $text,
            string $cssClasses = "",
            bool $allowHtml = false
    ): string {
        return self::alert($text, "alert-danger {$cssClasses}", $allowHtml);
    }

    public static function warning(string $text,
            string $cssClasses = "",
            bool $allowHtml = false): string {
        return self::alert($text, "alert-warning {$cssClasses}", $allowHtml);
    }

    public static function success(string $text,
            string $cssClasses = "",
            bool $allowHtml = false): string {
        return self::alert($text, "alert-success {$cssClasses}", $allowHtml);
    }

}
