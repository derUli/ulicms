<?php
$files = scandir ( dirname ( __file__ ) );
function startsWith($haystack, $needle) {
	// search backwards starting from haystack length characters from the end
	return $needle === "" || strrpos ( $haystack, $needle, - strlen ( $haystack ) ) !== FALSE;
}
function endsWith($haystack, $needle) {
	// search forward starting from end minus needle length characters
	return $needle === "" || (($temp = strlen ( $haystack ) - strlen ( $needle )) >= 0 && strpos ( $haystack, $needle, $temp ) !== FALSE);
}

foreach ( $files as $file ) {
	if (is_file ( $file ) and endsWith ( $file, ".php" ) and ! startswith ( file_get_contents ( $file ), "<?php" )) {
		echo $file;
		echo "\n";
	}
}