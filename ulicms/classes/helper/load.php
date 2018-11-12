<?php
$classes = array(
    "AntiSpamHelper",
    "ArrayHelper",
    "BackendHelper",
    "ModuleHelper",
    "NumberFormatHelper",
    "SecurityHelper",
    "StringHelper",
    "UrlHelper",
    "DataTablesHelper"
);
foreach ($classes as $class) {
    include_once dirname(__file__) . "/$class.php";
}