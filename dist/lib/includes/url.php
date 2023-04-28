<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

/**
 * Returns path to minified jQuery
 *
 * @return string
 */
function get_jquery_url(): string {
    $url = 'node_modules/jquery/dist/jquery.min.js';
    $url = apply_filter($url, 'jquery_url');
    return $url;
}

/**
 * Gets absolute shortlink URL in the foramt https://example.org/?goid=123
 *
 * @param int $id
 *
 * @return string|null
 */
function get_shortlink(?int $id = null): ?string {
    $shortlink = null;
    $id = $id ?: get_ID();

    if ($id) {
        $shortlink = getBaseFolderURL() . '/?goid=' . $id;
        $shortlink = apply_filter($shortlink, 'shortlink');
    }

    return $shortlink;
}

/**
 * Gets canonciel URL for current page
 *
 * @return string
 */
function get_canonical(): string {
    $canonical = getBaseFolderURL() . '/';
    if (! is_home()) {
        $canonical .= buildSEOUrl();
    }

    $canonical = apply_filter($canonical, 'canonical');
    return $canonical;
}

// TODO: this code works but looks like shit
// rewrite this method
function getBaseFolderURL(?string $suffix = null): string {
    $s = empty($_SERVER['HTTPS']) ? '' : (($_SERVER['HTTPS'] == 'on') ?
            's' : '');
    $sp = strtolower($_SERVER['SERVER_PROTOCOL']);
    $protocol = substr($sp, 0, strpos($sp, '/')) . $s;
    $port = ($_SERVER['SERVER_PORT'] == '80'
            || $_SERVER['SERVER_PORT'] == '443') ?
            '' : (':' . $_SERVER['SERVER_PORT']);
    $path = basename(dirname($_SERVER['REQUEST_URI'])) == '' ?
            $_SERVER['REQUEST_URI'] : dirname($_SERVER['REQUEST_URI']);
    $suffix = $suffix ?
            str_replace('\\', '/', $suffix) : str_replace('\\', '/', $path);
    return trim(
        rtrim(
            $protocol . '://'
            . $_SERVER['HTTP_HOST'] . $port
            .
            $suffix
        ),
        '/'
    );
}

// This Returns the current full URL
// for example: http://www.homepage.de/news?single=title
function getCurrentURL(): string {
    return getBaseFolderURL(get_request_uri());
}

/**
 * Generate path to Page
 * Argumente
 * String $page (Slug)
 * Rückgabewert String im Format
 * ../seite.html
 * bzw.
 * seite.html;
 */
function buildSEOUrl(
    ?string $page = null,
    ?string $redirection = null,
    // TODO: Obsoleten Parameter $format entfernen
    ?string $format = null
) {
    if ($redirection) {
        return $redirection;
    }
    if (! $page) {
        $page = get_slug();
    }
    if ($page === get_frontpage()) {
        return './';
    }

    $seo_url = '';

    if (is_admin_dir()) {
        $seo_url .= '../';
    }
    $seo_url .= $page;
    return $seo_url;
}
