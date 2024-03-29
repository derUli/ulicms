<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Constants\RequestMethod;

class Request {
    public static function getPort(): ?int {
        return isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : null;
    }

    public static function getProtocol(?string $suffix = null): string {
        $protocol = self::isSSL() ? 'https://' : 'http://';

        if (! empty($suffix)) {
            $protocol .= $suffix;
        }
        return $protocol;
    }

    public static function getVar(
        string $name,
        $default = null,
        ?string $convert = ''
    ) {
        $value = $default;
        if (isset($_POST[$name])) {
            $value = $_POST[$name];
        } elseif (isset($_GET[$name])) {
            $value = $_GET[$name];
        }

        if ($value !== null) {
            switch ($convert) {
                case 'bool':
                    if ($value === 'true') {
                        $value = true;
                    } elseif ($value === 'false') {
                        $value = false;
                    }
                    $value = (int)(bool)$value;
                    break;
                case 'int':
                    $value = (int)$value;
                    break;
                case 'float':
                    $value = (float)$value;
                    break;
                case 'str':
                    $value = (string)$value;
                    break;
            }
        }
        return $value;
    }

    public static function hasVar(string $name): bool {
        return isset($_POST[$name]) || isset($_GET[$name]);
    }

    public static function getMethod(): ?string {
        return isset($_SERVER['REQUEST_METHOD']) ?
                strtolower($_SERVER['REQUEST_METHOD']) : null;
    }

    /**
     * Check if this is a GET request
     * @return bool
     */
    public static function isGet(): bool {
        return self::getMethod() == RequestMethod::GET;
    }

    /**
     * Check if this is a POST request
     * @return bool
     */
    public static function isPost(): bool {
        return self::getMethod() == RequestMethod::POST;
    }

    /**
     * Check if this is a HEAD request
     * @return bool
     */
    public static function isHead(): bool {
        return self::getMethod() == RequestMethod::HEAD;
    }

    public static function isSSL(): bool {
        return (
            ! empty($_SERVER['HTTPS']) &&
            $_SERVER['HTTPS'] !== 'off') ||
                (
                    self::getPort() === 443
                );
    }

    public static function getIp(): ?string {
        $proxy_headers = self::getProxyHeaders();
        foreach ($proxy_headers as $proxy_header) {
            if (isset($_SERVER[$proxy_header])) {
                /**
                 * HEADER ist gesetzt und dies ist eine gültige IP
                 */
                return $_SERVER[$proxy_header];
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? null;
    }

    public static function isHeaderSent(string $header, ?array $headers = null): bool {
        $headers = ! $headers ? headers_list() : $headers;
        $header = trim($header, ': ');
        $result = false;

        foreach ($headers as $hdr) {
            if (stripos($hdr, $header) !== false) {
                $result = true;
            }
        }

        return $result;
    }

    public static function isAjaxRequest(): bool {
        return ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower(
                    $_SERVER['HTTP_X_REQUESTED_WITH']
                ) == 'xmlhttprequest';
    }

    public static function getDomain(): ?string {
        return $_SERVER['HTTP_HOST'] ??
                null;
    }

    public static function getReferrer(): ?string {
        $referrer = null;
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referrer = $_SERVER['HTTP_REFERER'];
        }
        return $referrer;
    }

    public static function getUserAgent(): ?string {
        return $_SERVER['HTTP_USER_AGENT'] ?? null;
    }

    public static function getRequestUri(): ?string {
        return $_SERVER['REQUEST_URI'] ?? null;
    }

    private static function getProxyHeaders(): array {
        return [
            'CLIENT_IP',
            'FORWARDED',
            'FORWARDED_FOR',
            'FORWARDED_FOR_IP',
            'HTTP_CLIENT_IP',
            'HTTP_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED_FOR_IP',
            'HTTP_PC_REMOTE_ADDR',
            'HTTP_PROXY_CONNECTION',
            'HTTP_VIA',
            'HTTP_X_FORWARDED',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED_FOR_IP',
            'HTTP_X_IMFORWARDS',
            'HTTP_XROXY_CONNECTION',
            'VIA',
            'X_FORWARDED',
            'X_FORWARDED_FOR'
        ];
    }
}
