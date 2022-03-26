<?php

$classes = array(
    "ModuleManager"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
