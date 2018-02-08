<?php
$classes = array (
		"AdminMenu",
		"MenuEntry" 
);
foreach ( $classes as $class ) {
	require_once dirname ( __FILE__ ) . "/$class.php";
}

