<?php
$classes = array(
    "ACL",
    "Encryption",
    "EntityPermissions"
);
foreach ($classes as $class) {
    require_once dirname(__FILE__) . "/$class.php";
}

