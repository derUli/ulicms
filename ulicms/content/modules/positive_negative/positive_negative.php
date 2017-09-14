<?php
function is_positive($value, $zeroPositive = false) {
	if ($zeroPositive) {
		return (is_numeric ( $value ) and $value >= 0);
	}
	return (is_numeric ( $value ) and $value > 0);
}
function is_negative($value) {
	return (is_numeric ( $value ) and $value < 0);
}