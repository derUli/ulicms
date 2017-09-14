<?php
function prepend_scheme($url, $scheme = 'http://') {
	return parse_url ( $url, PHP_URL_SCHEME ) === null ? $scheme . $url : $url;
}