<?php

$classes = array(
    "PackageManager",
    "SinPackageInstaller",
    "extend/AvailablePackageVersionMatcher"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
