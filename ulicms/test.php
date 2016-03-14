<?php
include_once "init.php";
$content = ContentFactory::getAllByMenuAndLanguage("bottom", "de");
$content = ContentFactory::filterByEnabled($content);
var_dump($content);