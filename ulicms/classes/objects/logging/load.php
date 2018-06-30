<?php
$classes = array (
		"Logger" 
);
foreach ( $classes as $class ) {
    require dirname ( __FILE__ ) . "/$class.php";
}

