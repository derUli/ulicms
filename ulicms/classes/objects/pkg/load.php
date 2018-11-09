<?php
$classes = array(
    "PackageManager",
    "SinPackageInstaller"
);
foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}

