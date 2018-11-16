<?php
$classes = array(
    "Group",
    "PasswordReset",
    "User",
    "UserManager"
);
foreach ($classes as $class) {
    include_once dirname(__file__) . "/$class.php";
}

