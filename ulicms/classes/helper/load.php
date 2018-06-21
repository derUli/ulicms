<?php
$classes = array(
    "AntiSpamHelper",
    "ArrayHelper",
    "BackendHelper",
    "HtmlHelper",
    "ModuleHelper",
    "NumberFormatHelper",
    "SecurityHelper",
    "StringHelper",
    "UrlHelper"
);
foreach ($classes as $class) {
    require_once dirname(__FILE__) . "/$class.php";
}