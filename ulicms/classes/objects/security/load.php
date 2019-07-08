<?php

$classes = array(
    "ACL",
    "Encryption",
    "PermissionChecker",
    "IDatasetPermissionChecker",
    "ContentPermissionChecker",
    "TwoFactorAuthentication"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
