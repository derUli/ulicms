<?php

$classes = array(
    "PackageManager",
    "SinPackageInstaller",
    "PackageSourceConnector",
    "Patch",
    "PatchManager"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}

