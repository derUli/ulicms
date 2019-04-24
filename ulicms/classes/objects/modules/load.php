<?php

$classes = array(
    "Module",
    "ModuleManager"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}

