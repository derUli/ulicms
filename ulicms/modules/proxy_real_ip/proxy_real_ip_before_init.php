<?php
<<<<<<< HEAD
/*
Plugin Name: Proxy Real IP
Plugin URI: http://wordpress.org/extend/plugins/proxy-real-ip/
Description: Correct the user's IP address if you are behind a proxy or load balancer. It should start working as soon as you activate it. Based on 'Real IP' plugin (v1.3) by Yejun Yang that is no longer updated.
Version: 1.1
Author: Sam Parsons
Author URI: http://sjparsons.com/
License: GPL2
*/

/*  Copyright 2012 Sam Parsons (sjparsons -at- gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

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
=======
if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
   $_SERVER["REMOTE_ADDR"] = $_SERVER['HTTP_X_FORWARDED_FOR'];
   unset($_SERVER['HTTP_X_FORWARDED_FOR']);
} 
>>>>>>> efb38284e077dafd648fd0602bc306dff3757e31
