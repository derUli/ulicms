<?php
$classes = array (
		"HttpStatusCode",
		"Mailer",
		"Request" 
);
foreach ( $classes as $class ) {
	require_once dirname ( __FILE__ ) . "/$class.php";
}

