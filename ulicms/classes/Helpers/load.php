<?php

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

$classes = [
    "ModuleHelper",
    "StringHelper",
];

foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
