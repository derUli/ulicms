<?php

$classes = [
    "CustomField",
    "TextField",
    "MultilineTextField",
    "UrlField",
    "EmailField",
    "MonthField",
    "DatetimeField",
    "NumberField",
    "ColorField",
    "HtmlField",
    "SelectField",
    "MultiSelectField",
    "CheckboxField",
    "FileFile",
    "FileImage"
];

foreach ($classes as $class) {
    require_once dirname(__FILE__) . "/$class.php";
}
