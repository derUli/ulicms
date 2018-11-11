<?php
$classes = array(
    "AuditLog",
    "EmailModes",
    "CommentStatus"
);
foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}

