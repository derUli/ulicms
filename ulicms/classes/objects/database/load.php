<?php
$classes = array(
    "Database",
    "DBMigrator"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}

