<?php

$classes = [
    "Request",
    "Response",
];

foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
