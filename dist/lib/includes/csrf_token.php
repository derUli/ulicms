<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

/**
 * Get hidden fields for CSRF check
 * 
 * @return string
 */
function get_csrf_token_html(): string {
    // CSRF Token
    $html = '<input type="hidden" name="csrf_token" value="' .
            get_csrf_token() . '">';

    /**
     * Optional security feature:
     * Count seconds between page load and submit and requre a minimum difference
     */
    if (Settings::get('min_time_to_fill_form', 'int') > 0) {
        $html .= '<input type="hidden" name="form_timestamp" value="' .
                time() . '">';
    }

    return optimizeHtml($html);
}

/**
 * Output hidden fields for CSRF check
 * 
 * @return void
 */
function csrf_token_html(): void {
    echo get_csrf_token_html();
}

/**
 * Get CSRF token
 * 
 * @return string
 */
function get_csrf_token(): string {
    if (! isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = md5(uniqid());
    }
    return $_SESSION['csrf_token'];
}

/**
 * Compare check CSRF token
 * 
 * @return bool
 */
function check_csrf_token(): bool {
    if (! isset($_REQUEST['csrf_token'])) {
        return false;
    }
    return $_REQUEST['csrf_token'] == $_SESSION['csrf_token'];
}
