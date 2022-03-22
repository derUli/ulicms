<?php

$classes = array(
    "Group",
    "User",
    "UserManager",
    "Session"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
