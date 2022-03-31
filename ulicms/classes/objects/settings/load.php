<?php

$classes = array(
    "Settings"
);

foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
