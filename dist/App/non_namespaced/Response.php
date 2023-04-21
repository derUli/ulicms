<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

$url = $_SERVER['REQUEST_URI'] ?? '';
$urlParts = explode('/' , $url);
$isFm = in_array('fm', $urlParts);

if (! $isFm) {
    class Response
    {
        public static function sendHttpStatusCodeResultIfAjax(
            int $status = HttpStatusCode::OK,
            ?string $redirect = null,
            int $redirectStatus = HttpStatusCode::MOVED_TEMPORARILY
        ): void {

            if (Request::isAjaxRequest()) {
                HTTPStatusCodeResult($status);
            }

            if ($redirect) {
                self::redirect($redirect, $redirectStatus);
            }
        }

        // Weiterleitung per Location header;
        public static function redirect(
            string $url = 'http://www.ulicms.de',
            int $status = HttpStatusCode::MOVED_TEMPORARILY
        ): void {
            self::sendStatusHeader($status);
            send_header('Location: ' . $url);
            exit();
        }

        public static function redirectToAction(
            string $action,
            ?string $controller = null,
            $status = HttpStatusCode::MOVED_TEMPORARILY
        ): void {
            if ($controller == null) {
                self::redirect(ModuleHelper::buildActionURL($action), $status);
            }
            self::redirect(
                ModuleHelper::buildMethodCallUrl(
                    $controller,
                    $action
                ),
                $status
            );
        }

        public static function javascriptRedirect(
            string $url = 'http://www.ulicms.de'
        ): void {
            echo '<script>'
            . "location.replace(\"{$url}\");</script>";
            echo '<noscript><p>' . get_translation('jsredirect_noscript', [
                '%url%' => Template::getEscape($url)
            ]) . '</p></noscript>';
        }

        public static function getSafeRedirectURL(
            string $url,
            $safeHosts = null
        ): string {


            if (is_array($safeHosts) && count($safeHosts) >= 1) {
                $safeHosts = $safeHosts;
            } elseif (isset($_ENV['SAFE_HOSTS'])) {
                $safeHosts = explode('; ', $_ENV['SAFE_HOSTS']);
                $safeHosts = array_map('trim', $safeHosts);
            } else {
                $safeHosts = [
                    get_http_host()
                ];
            }
            $host = parse_url($url, PHP_URL_HOST);
            if (! in_array($host, $safeHosts)) {
                try {
                    $page = ContentFactory::getBySlugAndLanguage(
                        Settings::getLanguageSetting(
                            'frontpage',
                            getCurrentLanguage()
                        ),
                        getCurrentLanguage()
                    );
                    $url = ModuleHelper::getFullPageURLByID($page->id);
                } catch (Exception $e) {
                    $url = ModuleHelper::getBaseUrl();
                }
            }
            return $url;
        }

        public static function safeRedirect(
            string $url,
            int $status = 302,
            $safeHosts = null
        ): void {
            $url = self::getSafeRedirectUrl($url, $safeHosts);
            self::redirect($url, $status);
        }

        public static function sendStatusHeader(?int $nr): bool
        {
            if (headers_sent()) {
                return false;
            }
            send_header($_SERVER['SERVER_PROTOCOL'] . ' ' .
                    self::getStatusCodeByNumber($nr));
            return true;
        }

        // Übersetzung HTTP Status Code => Name
        public static function getStatusCodeByNumber(int $nr): string
        {
            $http_codes = [
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
            ];
            return $nr . ' ' . $http_codes[$nr];
        }

        public static function sendHeader(string $header): bool
        {
            if (headers_sent() || is_cli()) {
                return false;
            }

            header($header);
            return true;
        }
    }
}
