<?php

$classes = array(
    "ACL",
    "XSSProtection"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
