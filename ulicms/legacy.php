<?php

// will be replaced with Settings::get()
function env($key) {
	$connection = MYSQL_CONNECTION;
	$key = db_escape ( $key );
	$query = db_query ( "SELECT * FROM " . tbname ( "settings" ) . " WHERE name='$key'", $connection );
	if (db_num_rows ( $query ) > 0) {
		while ( $row = db_fetch_object ( $query ) ) {
			return $row->value;
		}
	} else {
		return false;
	}
}
function print_env($ikey) {
	$value = env ( $ikey );
	if ($value != false) {
		echo $value;
	}
}
