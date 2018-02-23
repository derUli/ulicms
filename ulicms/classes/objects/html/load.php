<?php
$classes = array (
		"Style",
		"Script" 
);
foreach ( $classes as $class ) {
	require_once dirname ( __FILE__ ) . "/$class.php";
}

