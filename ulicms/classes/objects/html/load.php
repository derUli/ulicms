<?php
$classes = array (
		"Style",
		"Script",
		"Link",
		"Input" 
);
foreach ( $classes as $class ) {
	require_once dirname ( __FILE__ ) . "/$class.php";
}

