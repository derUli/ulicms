<?php

/**
 * Gibt ein Input Field mit dem CSRF Token zurück
 * @return string
 */
function get_csrf_token_html(): string {
    $html = '<input type="hidden" name="csrf_token" value="' .
            get_csrf_token() . '">';
    if (Settings::get("min_time_to_fill_form", "int") > 0) {
        $html .= '<input type="hidden" name="form_timestamp" value="' .
                time() . '">';
    }

    return optimizeHtml($html);
}

/**
 * Gibt ein Input Field mit dem CSRF Token aus
 * @return void
 */
function csrf_token_html(): void {
    echo get_csrf_token_html();
}

/**
 * Gibt den CSRF Token für die aktuelle Sitzung zurück
 * Wenn noch keiner vorhanden ist, wird dieser generiert
 * @return string CSRF Token
 */
function get_csrf_token(): string {
    if (!isset($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = md5(uniqid());
    }

    return $_SESSION["csrf_token"];
}

/**
 * Prüft, ob der übergebene CSRF Token korrekt ist.
 * @return bool
 */
function check_csrf_token(): bool {
    if (!isset($_REQUEST["csrf_token"])) {
        return false;
    }

    return $_REQUEST["csrf_token"] == $_SESSION["csrf_token"];
}

/**
 * Deaktiviert den CSRF Check für diesen Request
 * @return void
 */
function no_anti_csrf(): void {
    if (!defined("NO_ANTI_CSRF")) {
        define("NO_ANTI_CSRF", true);
    }
}
