<?php

$classes = array(
    "Path"
);

foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
