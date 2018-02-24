<?php
$classes = array (
		"Style",
		"Script",
		"Link",
		"ListItem",
		"Input" 
);
foreach ( $classes as $class ) {
	require_once dirname ( __FILE__ ) . "/$class.php";
}

