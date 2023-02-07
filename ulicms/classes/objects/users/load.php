<?php

$classes = array(
    "Group",
    "PasswordReset",
    "User",
    "UserManager",
    "GroupCollection",
    "Session"
);
foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}
