<?php

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

$classes = [
    "Request",
    "Response",
];

foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
