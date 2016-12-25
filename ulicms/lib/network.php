<?php
// returns site protocl
// http:// or https://
function get_site_protocol() {
	if (isset ( $_SERVER ['HTTPS'] ) && ($_SERVER ['HTTPS'] == 'on' || $_SERVER ['HTTPS'] == 1) || isset ( $_SERVER ['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER ['HTTP_X_FORWARDED_PROTO'] == 'https')
		return $protocol = 'https://';
	else
		return $protocol = 'http://';
}
function site_protocol() {
	echo get_site_protocol ();
}

function get_protocol_and_domain() {
	return get_site_protocol () . get_domain ();
}
function get_domain() {
	return $_SERVER ['SERVER_NAME'];
}

// Die IP-Adresse des Clients zurückgeben
// Falls ein Proxy genutzt wurde, versuchen, die echte IP statt der
// des Proxy zu ermitteln
function get_ip() {
	$proxy_headers = array (
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
	foreach ( $proxy_headers as $proxy_header ) {
		if (isset ( $_SERVER [$proxy_header] )) {
			/**
			 * HEADER ist gesetzt und dies ist eine gültige IP
			 */
			return $_SERVER [$proxy_header];
		} else if (stristr ( ',', $_SERVER [$proxy_header] ) !== false) {
			// Behandle mehrere IPs in einer Anfrage
			// (z.B.: X-Forwarded-For: client1, proxy1, proxy2)
			$proxy_header_temp = trim ( array_shift ( explode ( ',', $_SERVER [$proxy_header] ) ) );
			/**
			 * Teile in einzelne IPs, gib die letzte zurück und entferne Leerzeichen
			 */
			
			// if IPv4 address remove port if exists
			if (preg_match ( $regEx, $proxy_header_temp ) && ($pos_temp = stripos ( $proxy_header_temp, ':' )) !== false) {
				$proxy_header_temp = substr ( $proxy_header_temp, 0, $pos_temp );
			}
			return $proxy_header_temp;
		}
	}
	return $_SERVER ['REMOTE_ADDR'];
}

if (! function_exists ( "get_host" )) {
	function get_host() {
		if ($host = $_SERVER ['HTTP_X_FORWARDED_HOST']) {
			$elements = explode ( ',', $host );
			
			$host = trim ( end ( $elements ) );
		} else {
			if (! $host = $_SERVER ['HTTP_HOST']) {
				if (! $host = $_SERVER ['SERVER_NAME']) {
					$host = ! empty ( $_SERVER ['SERVER_ADDR'] ) ? $_SERVER ['SERVER_ADDR'] : '';
				}
			}
		}
		
		// Remove port number from host
		$host = preg_replace ( '/:\d+$/', '', $host );
		
		return trim ( $host );
	}
}


// Übersetzung HTTP Status Code => Name
function getStatusCodeByNumber($nr) {
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

function get_request_uri() {
	return $_SERVER ["REQUEST_URI"];
}
function get_http_host() {
	return $_SERVER ["HTTP_HOST"];
}
function get_referer() {
	return get_referrer ();
}
function get_referrer() {
	$referrer = null;
	if (isset ( $_SERVER ['HTTP_REFERER'] )) {
		$referrer = $_SERVER ['HTTP_REFERER'];
	}
	return $referrer;
}

function get_useragent() {
	return $_SERVER ['HTTP_USER_AGENT'];
}
function get_request_method() {
	return $_SERVER ["REQUEST_METHOD"];
}
function is_ajax_request() {
	return (! empty ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) && strtolower ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest');
}

// Weiterleitung per Location header;
function ulicms_redirect($url = "http://www.ulicms.de", $status = 302) {
	header ( "HTTP/1.0 " . getStatusCodeByNumber ( $status ) );
	header ( "Location: " . $url );
	exit ();
}

// Check for Secure HTTP Connection (SSL)
function is_ssl() {
	return (! empty ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] !== 'off' || $_SERVER ['SERVER_PORT'] == 443);
}