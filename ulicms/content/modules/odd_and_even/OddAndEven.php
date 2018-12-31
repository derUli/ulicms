<?php

namespace OddAndEven;

function isEven($val) {
	if (! is_numeric ( $val )) {
		throw new \InvalidArgumentException ( "$val is nut numeric." );
	}
	$val = round ( $val );
	return $val % 2 === 0;
}
function isOdd($val) {
	return ! isEven ( $val );
}
