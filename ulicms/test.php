<?php
include_once "init.php";
$page = new Page ();
$page->loadBySystemnameAndLanguage("about", "de");
$page->title = "Infos";
$page->alternate_title = "Infos über Uli";
$page->target = "_blank";
$page->save();
$page->undelete();

/*
$page->systemname = "Über mich";
$page->language = "de";
$page->save ();
*/