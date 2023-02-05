<?php

// HTML Code für Anti CSRF Token zurückgeben
// Siehe http://de.wikipedia.org/wiki/Cross-Site-Request-Forgery
function get_csrf_token_html(): string
{
    $html = '<input type="hidden" name="csrf_token" value="' .
            get_csrf_token() . '">';
    if (Settings::get("min_time_to_fill_form", "int") > 0) {
        $html .= '<input type="hidden" name="form_timestamp" value="' .
                time() . '">';
    }

    return optimizeHtml($html);
}

function csrf_token_html(): void
{
    echo get_csrf_token_html();
}

function get_csrf_token(): string
{
    if (!isset($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = md5(uniqid());
    }
    return $_SESSION["csrf_token"];
}

// Prüfen, ob Anti CSRF Token vorhanden ist
// Siehe http://de.wikipedia.org/wiki/Cross-Site-Request-Forgery
function check_csrf_token(): bool
{
    if (!isset($_REQUEST["csrf_token"])) {
        return false;
    }
    return $_REQUEST["csrf_token"] == $_SESSION["csrf_token"];
}
