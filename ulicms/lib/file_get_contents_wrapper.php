<?php

declare(strict_types=1);

use UliCMS\Exceptions\CorruptDownloadException;

/**
 * Fetch Data from URL by CURL
 * @param string $url URL
 * @return string|null response body or null
 */
function file_get_contents_curl(string $url): ?string {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERAGENT, ULICMS_USERAGENT);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

    // Set curl to return the data instead of printing it to the browser.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);

    $data = curl_exec($ch);

    if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200
            and curl_getinfo($ch, CURLINFO_HTTP_CODE) != 304
            and curl_getinfo($ch, CURLINFO_HTTP_CODE) != 302) {
        $data = null;
    }

    curl_close($ch);
    return $data;
}

/**
 * Checks if a string is an URL
 * @param string|null $url String to check
 * @return bool Returns true if this is an URL
 */
function is_url(?string $url): bool {
    if (!$url) {
        return false;
    }

    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Get file from URL or Path
 * @param string $url URL Or Path
 * @param bool $no_cache If this file should be cached
 * @param type $checksum 
 * @return string|null
 * @throws CorruptDownloadException
 */
function file_get_contents_wrapper(
        string $url,
        bool $no_cache = false,
        $checksum = null
): ?string {
    $content = false;
    if (!is_url($url)) {
        return file_exists($url) ? file_get_contents($url) : null;
    }
    $cache_name = md5($url);
    $cache_folder = PATH::resolve("ULICMS_CACHE");
    $cache_path = $cache_folder . "/" . $cache_name;
    if (is_file($cache_path) && is_url($url) && !$no_cache) {
        $content = file_get_contents($cache_path);
        return is_string($content) ? $content : null;
    }

    $content = file_get_contents_curl($url);

    if ($content and StringHelper::isNotNullOrWhitespace($checksum)
            and md5($content) !== strtolower($checksum)) {
        throw new CorruptDownloadException(
                        "Download of $url - Checksum validation failed"
        );
    }

    if (is_dir($cache_folder) and is_url($url) && !$no_cache) {
        file_put_contents($cache_path, $content);
    }

    return $content;
}

/**
 * Check if an URL exists by CURL
 * @param string $url URL
 * @return bool True if http status is in 2xx or 3xx range
 */
function curl_url_exists(string $url): bool {
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

    return $http_code >= 200 && $http_code < 400;
}

/**
 * Check if an URL exists
 * @param string $url URL
 * @return bool True if http status is in 2xx or 3xx range
 */
function url_exists(string $url): bool {
    return curl_url_exists($url);
}
