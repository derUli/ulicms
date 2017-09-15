<?php
function is_absolute_path($path) {
	if ($path === null || $path === '') {
		throw new Exception ( "Empty path" );
	}
	return $path [0] === DIRECTORY_SEPARATOR || preg_match ( '~\A[A-Z]:(?![^/\\\\])~i', $path ) > 0;
}