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
