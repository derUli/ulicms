<?php
$classes = array(
    "PackageManager",
    "SinPackageInstaller"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}

