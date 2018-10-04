<?php
$classes = array(
    "ACL",
    "Encryption",
    "PermissionChecker"
);
foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}

