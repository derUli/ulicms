<?php

$classes = array(
    "AuditLog",
    "EmailModes",
    "CommentStatus",
    "RequestMethod",
    "ModuleEventConstants",
    "AllowedTags"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}

