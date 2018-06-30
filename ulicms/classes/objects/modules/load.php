<?php
$classes = array (
		"Module",
		"ModuleManager" 
);
foreach ( $classes as $class ) {
    require dirname ( __FILE__ ) . "/$class.php";
}

