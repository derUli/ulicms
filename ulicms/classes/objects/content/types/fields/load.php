<?php

$classes = [
    "CustomField",
    "TextField",
    "MultilineTextField",
    "EmailField",
    "MonthField",
    "DatetimeField",
    "NumberField",
    "ColorField",
    "HtmlField",
    "SelectField",
    "CheckboxField",
    "FileFile",
    "FileImage"
];

foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
