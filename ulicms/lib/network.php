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
    return Request::getStatusCodeByNumber($nr);
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
