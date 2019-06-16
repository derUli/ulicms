<?php

$classes = array(
    "ACL",
    "Encryption",
    "PermissionChecker",
    "IDatasetPermissionChecker",
    "ContentPermissionChecker"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
