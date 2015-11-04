<?php 
include_once "init.php";
include_once "templating.php";
error_reporting(E_ALL);
  var_dump(buildSEOUrl("welcome", null, "pdf"));
  var_dump(get_format());
  set_format("pdf");
  var_dump(get_format());
  set_format(".txt");
  var_dump(get_format());
  exit();