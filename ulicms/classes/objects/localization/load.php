<?php
$classes = array(
    "JSTranslation",
    "Translation"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}

