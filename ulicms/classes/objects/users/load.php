<?php

$classes = array(
    "Group",
    "PasswordReset",
    "User",
    "UserManager",
    "GroupCollection"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
