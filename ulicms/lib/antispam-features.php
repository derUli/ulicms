<?php

// String contains chinese chars?
function is_chinese($str) {
	return AntispamHelper::isChinese ( $str );
}

// checking if this Country is blocked by spamfilter
function isCountryBlocked() {
	return AntispamHelper::isCountryBlocked ();
}
function checkForSpamhaus($host = null) {
	return AntispamHelper::checkForSpamhaus ( $host );
}