<?php
$classes = array (
		"ACL",
		"Encryption" 
);
foreach ( $classes as $class ) {
	require_once dirname ( __FILE__ ) . "/$class.php";
}

