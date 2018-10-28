<?php
$classes = array(
    "ACL",
    "Encryption",
    "PermissionChecker",
    "IDatasetPermissionChecker",
    "ContentPermissionChecker"
);
foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}
