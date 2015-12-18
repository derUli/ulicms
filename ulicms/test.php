<?php 
include_once "init.php";
var_dump(Settings::get("blubb"));
var_dump(Settings::init("blubb", "bla bla"));
var_dump(Settings::get("blubb"));
var_dump(Settings::set("blubb", "Ulis löbliche Heimseite"));
var_dump(Settings::get("blubb"));
var_dump(Settings::delete("blubb"));
var_dump(Settings::delete("blubb"));