<?php

namespace OddAndEven;

function is_even($val) {
	if (! is_numeric ( $val )) {
		throw new \InvalidArgumentException ( "$val is nut numeric." );
	}
	$val = round ( $val );
	return $val % 2 === 0;
}
function is_odd($val) {
	return ! is_even ( $val );
}
