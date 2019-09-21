<?php

declare(strict_types=1);

class Request {

    public static function getPort(): ?int {
        return isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : null;
    }

    public static function getProtocol(?string $suffix = null): string {
        $protocol = self::isSSL() ? "https://" : "http://";

        if (StringHelper::isNotNullOrWhitespace($suffix)) {
            $protocol .= $suffix;
        }
        return $protocol;
    }

    public static function getVar(string $name,
            $default = null,
            ?string $convert = "") {
        $value = $default;
        if (isset($_POST[$name])) {
            $value = $_POST[$name];
        } else if (isset($_GET[$name])) {
            $value = $_GET[$name];
        }

        if ($value !== null) {
            switch ($convert) {
                case "bool":
                    $value = intval($value);
                    break;
                case "int":
                    $value = intval($value);
                    break;
                case "float":
                    $value = floatval($value);
                    break;
                case "str":
                    $value = strval($value);
                    break;
            }
        }
        return $value;
    }

    public static function hasVar(string $name): bool {
        return (isset($_POST[$name]) or isset($_GET[$name]));
    }

// Übersetzung HTTP Status Code => Name
    public static function getStatusCodeByNumber(int $nr): string {
        return Response::getStatusCodeByNumber($nr);
    }

// Weiterleitung per Location header;
    public static function redirect(string $url = "http://www.ulicms.de",
            int $status = 302): void {
        Response::redirect($url, $status);
    }

    public static function javascriptRedirect(
            string $url = "http://www.ulicms.de"): void {
        Response::javascriptRedirect($url);
    }

    public static function getMethod(): ?string {
        return isset($_SERVER["REQUEST_METHOD"]) ?
                strtolower($_SERVER["REQUEST_METHOD"]) : null;
    }

    public static function isGet(): bool {
        return self::getMethod() == "get";
    }

    public static function isPost(): bool {
        return self::getMethod() == "post";
    }

    public static function isHead(): bool {
        return self::getMethod() == "head";
    }

    public static function isSSL(): bool {
        return (!empty($_SERVER['HTTPS']) &&
                $_SERVER['HTTPS'] !== 'off' ||
                $_SERVER['SERVER_PORT'] == 443);
    }

    private static function getProxyHeaders(): array {
        return array(
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
        );
    }

    public static function getIp(): string {
        $proxy_headers = self::getProxyHeaders();
        foreach ($proxy_headers as $proxy_header) {
            if (isset($_SERVER[$proxy_header])) {
                /**
                 * HEADER ist gesetzt und dies ist eine gültige IP
                 */
                return $_SERVER[$proxy_header];
            }
        }
        return $_SERVER['REMOTE_ADDR'];
    }

    public static function isHeaderSent(string $header): bool {
        $headers = headers_list();
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
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) and
                strtolower(
                        $_SERVER['HTTP_X_REQUESTED_WITH']
                ) == 'xmlhttprequest');
    }

    public static function getDomain(): ?string {
        return isset($_SERVER['HTTP_HOST']) ?
                $_SERVER['HTTP_HOST'] :
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
        return isset($_SERVER['HTTP_USER_AGENT']) ?
                $_SERVER['HTTP_USER_AGENT'] : null;
    }

    public static function getRequestUri(): ?string {
        return isset($_SERVER["REQUEST_URI"]) ?
                $_SERVER["REQUEST_URI"] : null;
    }

}
