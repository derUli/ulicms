<?php
$classes = array (
		"ACL",
		"Encryption" 
);
foreach ( $classes as $class ) {
    require dirname ( __FILE__ ) . "/$class.php";
}

