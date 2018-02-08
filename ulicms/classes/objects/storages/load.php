<?php
$classes = array (
		"Flags",
		"SettingsCache",
		"Vars",
		"ViewBag" 
);
foreach ( $classes as $class ) {
	require_once dirname ( __FILE__ ) . "/$class.php";
}

