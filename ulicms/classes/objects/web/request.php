<?php
class Request {
	public static function getProtocol($suffix = null) {
		$protocol = "http://";
		if (self::isSSL ()) {
			$protocol = "https://";
		}
		if (StringHelper::isNotNullOrWhitespace ( $suffix )) {
			$protocol .= $suffix;
		}
		return $protocol;
	}
	public static function getVar($name, $default = null, $convert = "") {
		$value = $default;
		if (isset ( $_POST [$name] )) {
			$value = $_POST [$name];
		} else if (isset ( $_GET [$name] )) {
			$value = $_GET [$name];
		}
		if ($value !== null) {
			switch ($convert) {
				case "int" :
					$value = intval ( $value );
					break;
				case "float" :
					$value = floatval ( $value );
					break;
				case "str" :
					$value = strval ( $value );
					break;
			}
		}
		return $value;
	}
	public static function hasVar($name) {
		return (isset ( $_POST [$name] ) or isset ( $_GET [$name] ));
	}
	
	// Übersetzung HTTP Status Code => Name
	public static function getStatusCodeByNumber($nr) {
		$http_codes = array (
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
		return $nr . " " . $http_codes [$nr];
	}
	// Weiterleitung per Location header;
	public static function redirect($url = "http://www.ulicms.de", $status = 302) {
		header ( "HTTP/1.0 " . self::getStatusCodeByNumber ( $status ) );
		header ( "Location: " . $url );
		exit ();
	}
	public static function javascriptRedirect($url = "http://www.ulicms.de") {
		echo "<script type=\"text/javascript\">location.replace(\"$url\");</script>";
		exit ();
	}
	public static function getMethod() {
		return strtolower ( $_SERVER ["REQUEST_METHOD"] );
	}
	public static function isGet() {
		return self::getMethod () == "get";
	}
	public static function isPost() {
		return self::getMethod () == "post";
	}
	public static function isHead() {
		return self::getMethod () == "head";
	}
	public static function isSSL() {
		return (! empty ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] !== 'off' || $_SERVER ['SERVER_PORT'] == 443);
	}
	public static function getIp() {
		$ip = '';
		$sources = array (
				'REMOTE_ADDR',
				'HTTP_X_FORWARDED_FOR',
				'HTTP_CLIENT_IP' 
		);
		
		foreach ( $sources as $source ) {
			if (isset ( $_SERVER [$source] )) {
				$ip = $_SERVER [$source];
			} elseif (getenv ( $source )) {
				$ip = getenv ( $source );
			}
		}
		
		return $ip;
	}
}
