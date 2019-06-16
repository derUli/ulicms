<?php

$classes = array(
    "PackageManager",
    "SinPackageInstaller",
    "PackageSourceConnector"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}

