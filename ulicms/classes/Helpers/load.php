<?php

$classes = array(
    "AntiSpamHelper",
    "BackendHelper",
    "ModuleHelper",
    "StringHelper",
    "ImagineHelper"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
