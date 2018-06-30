<?php
$classes = array (
		"Style",
		"Script",
		"Link",
		"ListItem",
		"Input",
		"functions" 
);
foreach ( $classes as $class ) {
    require dirname ( __FILE__ ) . "/$class.php";
}

