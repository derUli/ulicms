<?php

declare(strict_types=1);

class_exists("\\Composer\\Autoload\\ClassLoader") || ('No direct script access allowed');

use App\Exceptions\CorruptDownloadException;
use App\Security\Hash;
use App\Utils\CacheUtil;
use Fetcher\Fetcher;

/**
 * Check if a given variable is an URL
 *
 * @deprecated since 2023.3
 * @param mixed $url
 * @return bool
 */
function is_url(mixed $url): bool
{
    return is_string($url) ? Fetcher::isUrl($url) : false;
}

/**
 * Retrieves an URL by curl if available or by file_get_contents
 *
 * @deprecated since 2023.3
 * @param string $url
 * @param bool $noCache
 * @param string|null $checksum
 *
 * @return string|null
 */
function file_get_contents_wrapper(
    string $url,
    bool $noCache = false,
    ?string $checksum = null
): ?string {
    $content = null;

    if (! is_url($url)) {
        return is_file($url) ? (string)file_get_contents($url) : null;
    }

    $cacheItemId = Hash::hashCacheIdentifier($url);

    $cacheAdapter = ! $noCache ? CacheUtil::getAdapter(true) : null;

    if ($cacheAdapter && is_string($cacheAdapter->get($cacheItemId))) {
        return $cacheAdapter->get($cacheItemId);
    }

    $fetcher = new Fetcher($url);
    $content = $fetcher->fetch();

    if ($content && ! empty($checksum) && md5($content) !== strtolower($checksum)
    ) {
        throw new CorruptDownloadException(
            "Download of {$url} - Checksum validation failed"
        );
    }

    if ($cacheAdapter) {
        $cacheAdapter->set($cacheItemId, $content);
    }

    return $content;
}

/**
 * Check if an URL exists
 *
 * @deprecated since 2023.3
 * @param string $url
 * @return bool
 */
function curl_url_exists(string $url): bool
{
    $fetcher = new Fetcher($url);

    return $fetcher->exists();
}
