<?php

class Request
{

    public static function getPort()
    {
        return $_SERVER['SERVER_PORT'];
    }

    public static function getProtocol($suffix = null)
    {
        $protocol = "http://";
        if (self::isSSL()) {
            $protocol = "https://";
        }
        if (StringHelper::isNotNullOrWhitespace($suffix)) {
            $protocol .= $suffix;
        }
        return $protocol;
    }

    public static function getVar($name, $default = null, $convert = "")
    {
        $value = $default;
        if (isset($_POST[$name])) {
            $value = $_POST[$name];
        } else if (isset($_GET[$name])) {
            $value = $_GET[$name];
        }
        if ($value !== null) {
            switch ($convert) {
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

    public static function hasVar($name)
    {
        return (isset($_POST[$name]) or isset($_GET[$name]));
    }

    // Übersetzung HTTP Status Code => Name
    public static function getStatusCodeByNumber($nr)
    {
        return Response::getStatusCodeByNumber($nr);
    }

    // Weiterleitung per Location header;
    public static function redirect($url = "http://www.ulicms.de", $status = 302)
    {
        Response::redirect($url, $status);
    }

    public static function javascriptRedirect($url = "http://www.ulicms.de")
    {
        Response::javascriptRedirect($url);
    }

    public static function getMethod()
    {
        return strtolower($_SERVER["REQUEST_METHOD"]);
    }

    public static function isGet()
    {
        return self::getMethod() == "get";
    }

    public static function isPost()
    {
        return self::getMethod() == "post";
    }

    public static function isHead()
    {
        return self::getMethod() == "head";
    }

    public static function isSSL()
    {
        return (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443);
    }

    public static function getIp()
    {
        $proxy_headers = array(
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
        $regEx = "/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/";
        foreach ($proxy_headers as $proxy_header) {
            if (isset($_SERVER[$proxy_header])) {
                /**
                 * HEADER ist gesetzt und dies ist eine gültige IP
                 */
                return $_SERVER[$proxy_header];
            } else if (stristr(',', $_SERVER[$proxy_header]) !== false) {
                // Behandle mehrere IPs in einer Anfrage
                // (z.B.: X-Forwarded-For: client1, proxy1, proxy2)
                $proxy_header_temp = trim(array_shift(explode(',', $_SERVER[$proxy_header])));
                /**
                 * Teile in einzelne IPs, gib die letzte zurück und entferne Leerzeichen
                 */
                
                // if IPv4 address remove port if exists
                if (preg_match($regEx, $proxy_header_temp) && ($pos_temp = stripos($proxy_header_temp, ':')) !== false) {
                    $proxy_header_temp = substr($proxy_header_temp, 0, $pos_temp);
                }
                return $proxy_header_temp;
            }
        }
        return $_SERVER['REMOTE_ADDR'];
    }

    public static function isHeaderSent($header)
    {
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

    public static function isAjaxRequest()
    {
        return (! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }
}
