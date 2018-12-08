<?php
$classes = array(
    "JSTranslation",
    "Translation"
);
foreach ($classes as $class) {
    include_once dirname(__file__) . "/$class.php";
}

