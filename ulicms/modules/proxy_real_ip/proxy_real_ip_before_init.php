<?php
$ip_match = '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/';

$http_headers = array(
    'HTTP_X_FORWARDED_FOR',
    'HTTP_X_FORWARDED',
    'HTTP_FORWARDED_FOR',
    'HTTP_FORWARDED',
    'HTTP_X_REAL_IP',
);

foreach($http_headers as $h) {
    if (isset($_SERVER[$h]) && preg_match($ip_match,$_SERVER[$h])){
       $_SERVER['REMOTE_ADDR'] = $_SERVER[$h];
       break;
   }
}    

// Set Server variables so that is_ssl() reports correctly.
// If HTTPS server variable is not set and the HTTP_X_FORWARDED_PROTO is set, then
// set HTTPS = 1 if HTTP_X_FORWARDED_PROTO is set

if (!isset($_SERVER['HTTPS'])) {
    if ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
        $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
        $_SERVER['HTTPS'] = 1;
    }
    else {
        $_SERVER['HTTPS'] = 0;       
    }
}


if ( ! empty( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) {
    $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
}