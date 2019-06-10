<?php

$classes = array(
    "AuditLog",
    "EmailModes",
    "CommentStatus",
    "RequestMethod",
    "ModuleEventConstants",
    "AllowedTags",
    "LinkTarget",
    "ButtonType",
    "PackageTypes",
    "ButtonType"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}

