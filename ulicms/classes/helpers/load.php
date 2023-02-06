<?php

$classes = [
    'AntiSpamHelper',
    'BackendHelper',
    'ImagineHelper',
    'ModuleHelper',
    'StringHelper'
];

foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
