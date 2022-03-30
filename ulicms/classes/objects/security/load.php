<?php

$classes = array(
    "ACL",
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
