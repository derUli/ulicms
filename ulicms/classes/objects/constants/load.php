<?php
$classes = array (
		"EmailModes" 
);
foreach ( $classes as $class ) {
    require dirname ( __FILE__ ) . "/$class.php";
}

