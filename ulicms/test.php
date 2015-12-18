<?php 
include_once "init.php";
var_dump(Settings::get("homepage_title"));
var_dump(Settings::init("homepage_title", "bla bla"));
var_dump(Settings::get("homepage_title"));
var_dump(Settings::set("homepage_title", "Ulis löbliche Heimseite"));
var_dump(Settings::get("homepage_title"));