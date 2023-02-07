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
    require_once dirname(__FILE__) . "/$class.php";
}
