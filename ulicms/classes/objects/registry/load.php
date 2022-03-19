<?php

$classes = array(
    "ControllerRegistry",
    "LoggerRegistry"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
