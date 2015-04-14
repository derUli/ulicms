<?php
function formatTime($Sekundenzahl) {
	$Sekundenzahl = abs ( $Sekundenzahl ); // Ganzzahlwert bilden
	
	return sprintf ( TRANSLATION_FORMAT_TIME, $Sekundenzahl / 60 / 60 / 24, ($Sekundenzahl / 60 / 60) % 24, ($Sekundenzahl / 60) % 60, $Sekundenzahl % 60 );
}

// Snippet from PHP Share: http://www.phpshare.org
function formatSizeUnits($bytes) {
	if ($bytes >= 1073741824) {
		$bytes = number_format ( $bytes / 1073741824, 2 ) . ' GB';
	} elseif ($bytes >= 1048576) {
		$bytes = number_format ( $bytes / 1048576, 2 ) . ' MB';
	} elseif ($bytes >= 1024) {
		$bytes = number_format ( $bytes / 1024, 2 ) . ' KB';
	} elseif ($bytes > 1) {
		$bytes = $bytes . ' bytes';
	} elseif ($bytes == 1) {
		$bytes = $bytes . ' byte';
	} else {
		$bytes = '0 bytes';
	}
	
	return $bytes;
}
