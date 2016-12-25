<?php
// Ordner rekursiv kopieren
function recurse_copy($src, $dst) {
	$dir = opendir ( $src );
	@mkdir ( $dst );
	while ( false !== ($file = readdir ( $dir )) ) {
		if (($file != '.') && ($file != '..')) {
			if (is_dir ( $src . '/' . $file )) {
				recurse_copy ( $src . '/' . $file, $dst . '/' . $file );
			} else {
				copy ( $src . '/' . $file, $dst . '/' . $file );
			}
		}
	}
	closedir ( $dir );
}


function find_all_files($dir) {
	$root = scandir ( $dir );
	$result = array ();
	foreach ( $root as $value ) {
		if ($value === '.' || $value === '..') {
			continue;
		}
		if (is_file ( "$dir/$value" )) {
			$result [] = str_Replace ( "\\", "/", "$dir/$value" );
			continue;
		}
		foreach ( find_all_files ( "$dir/$value" ) as $value ) {
			$value = str_replace ( "\\", "/", $value );
			$result [] = $value;
		}
	}
	return $result;
}

function file_extension($filename) {
	$ext = explode ( ".", $filename );
	$ext = end ( $ext );
	return $ext;
}
