<?php

declare(strict_types=1);
use App\Utils\Mailer;

// returns site protocl
// http:// or https://
function get_site_protocol(): string
{
    return Request::getProtocol();
}

function site_protocol(): void
{
    echo get_site_protocol();
}

function get_protocol_and_domain(): string
{
    return get_site_protocol() . get_domain();
}

function get_domain(): ?string
{
    return Request::getDomain();
}

// Die IP-Adresse des Clients zurückgeben
// Falls ein Proxy genutzt wurde, versuchen, die echte IP statt der
// des Proxy zu ermitteln
function get_ip(): ?string
{
    return Request::getIp();
}

function get_host(): string
{
    if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
        $elements = explode(',', $host);
        $host = trim(end($elements));
    } else {
        $vars = [
            'HTTP_HOST',
            'SERVER_NAME',
            'SERVER_ADDR'
        ];

        foreach ($vars as $var) {
            if (isset($_SERVER[$var]) && !empty($_SERVER[$var])) {
                $host = $_SERVER[$var];
                break;
            }
        }
    }

    // Remove port number from host
    $host = preg_replace('/:\d+$/', '', $host);

    return trim($host);
}

// Übersetzung HTTP Status Code => Name
function getStatusCodeByNumber(int $nr)
{
    return Response::getStatusCodeByNumber($nr);
}

function get_request_uri(): ?string
{
    return Request::getRequestUri();
}

function get_http_host(): ?string
{
    return get_domain();
}

function get_referrer(): ?string
{
    return Request::getReferrer();
}

function get_useragent(): string
{
    return Request::getUserAgent();
}

function get_request_method(): string
{
    return Request::getMethod();
}

// Check for Secure HTTP Connection (SSL)
function is_ssl(): bool
{
    return Request::isSSL();
}

function send_header(string $header): bool
{
    $headers = Vars::get('http_headers');

    if (!$headers) {
        $headers = [];
    }
    if (!in_array($header, $headers)) {
        $headers[] = $header;
    }

    Vars::set('http_headers', $headers);

    return class_exists('Response') ? Response::sendHeader($header) : false;
}
