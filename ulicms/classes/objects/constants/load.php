<?php
$classes = array(
    "AuditLog",
    "EmailModes",
    "CommentStatus",
    "RequestMethod",
    "ModuleEventConstants"
);
foreach ($classes as $class) {
    include_once dirname(__file__) . "/$class.php";
}

