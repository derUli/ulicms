<?php

$classes = array(
    "Request",
    "Response"
);
foreach ($classes as $class) {
    require_once dirname(__FILE__) . "/$class.php";
}
