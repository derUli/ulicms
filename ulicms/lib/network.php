<?php

declare(strict_types=1);

// returns site protocl
// http:// or https://
function get_site_protocol(): string {
    return Request::getProtocol();
}

function site_protocol(): void {
    echo get_site_protocol();
}

function get_protocol_and_domain(): string {
    return get_site_protocol() . get_domain();
}

function get_domain(): ?string {
    return Request::getDomain();
}

// Die IP-Adresse des Clients zurückgeben
// Falls ein Proxy genutzt wurde, versuchen, die echte IP statt der
// des Proxy zu ermitteln
function get_ip(): string {
    return Request::getIp();
}

function get_host(): string {
    if ($host = $_SERVER['HTTP_X_FORWARDED_HOST']) {
        $elements = explode(',', $host);
        $host = trim(end($elements));
    } else {
        if (!$host = $_SERVER['HTTP_HOST']) {
            if (!$host = $_SERVER['SERVER_NAME']) {
                $host = !empty($_SERVER['SERVER_ADDR']) ?
                        $_SERVER['SERVER_ADDR'] : '';
            }
        }
    }

// Remove port number from host
    $host = preg_replace('/:\d+$/', '', $host);

    return trim($host);
}

// Übersetzung HTTP Status Code => Name
function getStatusCodeByNumber(int $nr) {
    $http_codes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended'
    );

    return $nr . " " . $http_codes[$nr];
}

function get_request_uri(): ?string {
    return Request::getRequestUri();
}

function get_http_host(): ?string {
    return get_domain();
}

function get_referer(): ?string {
    return get_referrer();
}

function get_referrer(): ?string {
    return Request::getReferrer();
}

function get_useragent(): string {
    return Request::getUserAgent();
}

function get_request_method(): string {
    return Request::getMethod();
}

// Check for Secure HTTP Connection (SSL)
function is_ssl(): bool {
    return Request::isSSL();
}

function ulicms_mail(string $to,
        string $subject,
        string $message,
        ?string $headers = ""): bool {
    return Mailer::send($to, $subject, $message, $headers);
}
