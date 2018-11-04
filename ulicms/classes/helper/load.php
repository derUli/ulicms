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
    require_once dirname(__FILE__) . "/$class.php";
}