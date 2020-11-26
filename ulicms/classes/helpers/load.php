<?php

$classes = array(
    "AntiSpamHelper",
    "ArrayHelper",
    "BackendHelper",
    "ModuleHelper",
    "NumberFormatHelper",
    "StringHelper",
    "UrlHelper",
    "DataTablesHelper",
    "TestHelper",
    "ImageScaleHelper"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
