<?php

$classes = array(
    "ControllerRegistry",
    "ModelRegistry",
    "LoggerRegistry"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
