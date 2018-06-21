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
        $ip = '';
        $sources = array(
            'REMOTE_ADDR',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_CLIENT_IP'
        );
        
        foreach ($sources as $source) {
            if (isset($_SERVER[$source])) {
                $ip = $_SERVER[$source];
            } elseif (getenv($source)) {
                $ip = getenv($source);
            }
        }
        
        return $ip;
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
