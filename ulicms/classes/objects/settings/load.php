<?php
$classes = array (
		"BaseConfig",
		"Settings" 
);
foreach ( $classes as $class ) {
    require dirname ( __FILE__ ) . "/$class.php";
}

