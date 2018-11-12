<?php
$classes = array(
    "Module",
    "ModuleManager"
);
foreach ($classes as $class) {
    include_once dirname(__file__) . "/$class.php";
}

