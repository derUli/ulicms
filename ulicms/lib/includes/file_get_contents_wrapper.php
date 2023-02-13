<?php

declare(strict_types=1);

defined('ULICMS_ROOT') or exit('no direct script access allowed');

use App\Utils\CacheUtil;
use App\Exceptions\CorruptDownloadException;

// die Funktionalität von file_get_contents
// mit dem Curl-Modul umgesetzt
function file_get_contents_curl(string $url): ?string
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERAGENT, ULICMS_USERAGENT);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

    // Set curl to return the data instead of printing it to the browser.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);

    $data = curl_exec($ch) ?? null;

    if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200 && curl_getinfo($ch, CURLINFO_HTTP_CODE) != 304 && curl_getinfo($ch, CURLINFO_HTTP_CODE) != 302) {
        $data = null;
    }

    curl_close($ch);
    return $data;
}

/**
 * Check if a given variable is an URL
 * @param mixed $url
 * @return bool
 */
function is_url(mixed $url): bool
{
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Retrieves an URL by curl if available or by file_get_contents
 * @param string $url
 * @param bool $no_cache
 * @param type $checksum
 * @return string|null
 * @throws CorruptDownloadException
 */
function file_get_contents_wrapper(
    string $url,
    bool $noCache = false,
    $checksum = null
): ?string {
    $content = null;

    if (!is_url($url)) {
        return is_file($url) ? file_get_contents($url) : null;
    }

    $cacheItemId = md5($url);

    $cacheAdapter = !$noCache ? CacheUtil::getAdapter(true) : null;

    if ($cacheAdapter && $cacheAdapter->has($cacheItemId)) {
        return $cacheAdapter->get($cacheItemId);
    }

    $content = file_get_contents_curl($url);

    if (
        $content &&
        StringHelper::isNotNullOrWhitespace($checksum) &&
        md5($content) !== strtolower($checksum)
    ) {
        throw new CorruptDownloadException(
            "Download of $url - Checksum validation failed"
        );
    }

    if ($cacheAdapter) {
        $cacheAdapter->set($cacheItemId, $content);
    }

    return $content;
}

function curl_url_exists(string $url): bool
{
    $timeout = 10;
    $ch = curl_init();
    // HTTP request is 'HEAD' too make this method fast
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_USERAGENT, ULICMS_USERAGENT);

    curl_exec($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    return $http_code >= 200 and $http_code < 400;
}