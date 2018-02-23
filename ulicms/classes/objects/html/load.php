<?php
$classes = array (
		"Style",
		"Script",
		"Link" 
);
foreach ( $classes as $class ) {
	require_once dirname ( __FILE__ ) . "/$class.php";
}

