<?php
$classes = array(
    "JSTranslation",
    "Translation"
);
foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}

