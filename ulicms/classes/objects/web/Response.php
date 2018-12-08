<?php

class Response
{

    // Weiterleitung per Location header;
    public static function redirect($url = "http://www.ulicms.de", $status = 302)
    {
        Response::sendStatusHeader(self::getStatusCodeByNumber($status));
        header("Location: " . $url);
        exit();
    }

    public static function javascriptRedirect($url = "http://www.ulicms.de")
    {
        echo "<script type=\"text/javascript\">location.replace(\"$url\");</script>";
        echo "<noscript><p>" . get_translation("jsredirect_noscript", array(
            "%url%" => Template::getEscape($url)
        )) . "</p></noscript>";
        exit();
    }

    public static function getSafeRedirectURL($url, $safeHosts = null)
    {
        $cfg = new CMSConfig();
        if (is_array($safeHosts) and count($safeHosts) >= 1) {
            $safeHosts = $safeHosts;
        } else if (isset($cfg->safe_hosts) and is_array($cfg->safe_hosts)) {
            $safeHosts = $cfg->safe_hosts;
        } else {
            $safeHosts = array(
                get_http_host()
            );
        }
        $host = parse_url($url, PHP_URL_HOST);
        if (! in_array($host, $safeHosts)) {
            try {
                $page = ContentFactory::getBySystemnameAndLanguage(Settings::getLang("frontpage", getCurrentLanguage()), getCurrentLanguage());
                $url = ModuleHelper::getFullPageURLByID($page->id);
            } catch (Exception $e) {
                $url = ModuleHelper::getBaseUrl();
            }
        }
        return $url;
    }

    public static function safeRedirect($url, $status = 302, $safeHosts = null)
    {
        $url = self::getSafeRedirectUrl($url, $safeHosts);
        Request::redirect($url, $status);
    }

    public static function sendStatusHeader($nr)
    {
        if (headers_sent()) {
            return false;
        }
        header($_SERVER["SERVER_PROTOCOL"] . " " . self::getStatusCodeByNumber($nr));
        return true;
    }

    // Übersetzung HTTP Status Code => Name
    public static function getStatusCodeByNumber($nr)
    {
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
}