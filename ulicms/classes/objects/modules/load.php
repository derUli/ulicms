<?php

$classes = array(
	"Theme",
	"Module",
	"ModuleManager"
);
foreach ($classes as $class) {
	require_once dirname(__file__) . "/$class.php";
}

