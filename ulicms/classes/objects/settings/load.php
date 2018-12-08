<?php
$classes = array(
    "BaseConfig",
    "Settings"
);
foreach ($classes as $class) {
    include_once dirname(__file__) . "/$class.php";
}

