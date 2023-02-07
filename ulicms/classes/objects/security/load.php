<?php

$classes = array(
    "ACL",
    "Encryption",
    "PermissionChecker",
    "IDatasetPermissionChecker",
    "ContentPermissionChecker",
    "XSSProtection"
);
foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}
