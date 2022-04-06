<?php

$classes = array(
    "Database"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
