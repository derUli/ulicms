<?php

namespace UliCMS\HTML;

class Alert {

    public static function alert($text, $cssClasses, $allowHtml = false) {
        if (!$allowHtml) {
            $text = _esc($text);
        }
        return "<div class=\"alert {$cssClasses}\">{$text}</div>";
    }

    public static function info($text, $cssClasses = "", $allowHtml = false) {
        return self::alert($text, "alert-info {$cssClasses}", $allowHtml);
    }

    public static function danger($text, $cssClasses = "", $allowHtml = false) {
        return self::alert($text, "alert-danger {$cssClasses}", $allowHtml);
    }

    public static function warning($text, $cssClasses = "", $allowHtml = false) {
        return self::alert($text, "alert-warning {$cssClasses}", $allowHtml);
    }

    public static function success($text, $cssClasses = "", $allowHtml = false) {
        return self::alert($text, "alert-success {$cssClasses}", $allowHtml);
    }

}
