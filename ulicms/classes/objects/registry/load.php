<?php

$classes = array(
    "ControllerRegistry"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
