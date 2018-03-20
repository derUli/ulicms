<?php
$classes = array (
		"ActionRegistry",
		"ControllerRegistry",
		"HelperRegistry",
		"ModelRegistry" 
);
foreach ( $classes as $class ) {
	require_once dirname ( __FILE__ ) . "/$class.php";
}

