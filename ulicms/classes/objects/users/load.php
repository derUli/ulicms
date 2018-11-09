<?php
$classes = array(
    "Group",
    "PasswordReset",
    "User",
    "UserManager"
);
foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}

