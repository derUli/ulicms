<?php
$classes = array (
		"Database",
		"DBMigrator" 
);
foreach ( $classes as $class ) {
    require dirname ( __FILE__ ) . "/$class.php";
}

