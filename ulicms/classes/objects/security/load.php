<?php

$classes = array(
    "XSSProtection"
);
foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}
