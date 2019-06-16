<?php

use UliCMS\Exceptions\CorruptDownloadException;

// die Funktionalität von file_get_contents
// mit dem Curl-Modul umgesetzt
function file_get_contents_curl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERAGENT, ULICMS_USERAGENT);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Set curl to return the data instead of printing it to the browser.
    curl_setopt($ch, CURLOPT_URL, $url);

    $data = curl_exec($ch);

    if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200 and curl_getinfo($ch, CURLINFO_HTTP_CODE) != 304 and curl_getinfo($ch, CURLINFO_HTTP_CODE) != 302) {
        $data = false;
    }

    curl_close($ch);
    return $data;
}

function is_url($url) {
    if (startsWith($url, "http://") or startsWith($url, "https://") or startsWith($url, "ftp://")) {
        return ($url != "http://" and $url != "https://" and $url != "ftp://");
    }
    return false;
}

// Nutze curl zum Download der Datei, sofern verfügbar
// Ansonsten Fallback auf file_get_contents
function file_get_contents_wrapper($url, $no_cache = false, $checksum = null) {
    $content = false;
    if (!is_url($url)) {
        return file_get_contents($url);
    }
    $cache_name = md5($url);
    $cache_folder = PATH::resolve("ULICMS_CACHE");
    $cache_path = $cache_folder . "/" . $cache_name;
    if (is_file($cache_path) && is_url($url) && !$no_cache) {
        return file_get_contents($cache_path);
    }

    // use file_get_contents() on Google Cloud Platform since it's optimized by Google
    $runningInGoogleCloud = class_exists("GoogleCloudHelper") ? GoogleCloudHelper::isProduction() : false;

    if (function_exists("curl_init") and is_url($url) and ! $runningInGoogleCloud) {
        $content = file_get_contents_curl($url);
    } else if (ini_get("allow_url_fopen")) {
        ini_set("default_socket_timeout", 5);
        $content = @file_get_contents($url, 0, $context);
    }

    if ($content and StringHelper::isNotNullOrWhitespace($checksum) and md5($content) !== strtolower($checksum)) {
        throw new CorruptDownloadException("Download of $url - Checksum validation failed");
    }

    if (is_dir($cache_folder) and is_url($url) and ! $no_cache) {
        file_put_contents($cache_path, $content);
    }

    return $content;
}

function url_exists($url) {
    if (@file_get_contents($url, FALSE, NULL, 0, 0) === false) {
        return false;
    }
    return true;
}
