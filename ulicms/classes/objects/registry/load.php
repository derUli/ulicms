<?php

$classes = array(
    "ActionRegistry",
    "ControllerRegistry",
    "ModelRegistry",
    "LoggerRegistry"
);
foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}
