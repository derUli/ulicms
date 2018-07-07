<?php
$classes = array(
    "AuditLog",
    "EmailModes"
);
foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}

