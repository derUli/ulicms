<?php
$classes = array(
    "BaseConfig",
    "Settings"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}

