<?php
$classes = array (
		"Audio",
		"Video" 
);
foreach ( $classes as $class ) {
    require dirname ( __FILE__ ) . "/$class.php";
}

