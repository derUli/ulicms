<?php
$classes = array(
    "PackageManager",
    "SinPackageInstaller"
);
foreach ($classes as $class) {
    include_once dirname(__file__) . "/$class.php";
}

